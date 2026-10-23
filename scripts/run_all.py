#!/usr/bin/env python3
"""Roda a bateria de testes de carga: cenários x usuários x instâncias do WordPress.

Para cada nº de instâncias: escala o WordPress, reinicia o nginx e confere o balanceamento.
Para cada cenário e nº de usuários: roda o Locust sem interface e grava o CSV em results/.
Execuções que já têm CSV são puladas (use --force para refazer), então dá para
interromper e continuar depois.

Exemplos:
  python3 scripts/run_all.py                                   # os 27 testes
  python3 scripts/run_all.py --users 10 --instances 1 --scenarios 1 --duration 10   # teste rápido
"""
import argparse
import csv
import json
import subprocess
import sys
import time
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
RESULTS = ROOT / "results"

USERS = [10, 100, 1000]
INSTANCES = [1, 2, 3]
SCENARIOS = [1, 2, 3]
DURATION = 60      # segundos medidos em cada execução
RAMP = 10          # segundos para subir todos os usuários (não entram nas métricas)
DEFAULT_INSTANCES = 3

POSTS = {
    1: "/index.php/2026/01/01/cenario-1-imagem-1mb/",
    2: "/index.php/2026/01/01/cenario-2-texto-400kb/",
    3: "/index.php/2026/01/01/cenario-3-imagem-300kb/",
}


def compose(*args, check=True, capture=False):
    return subprocess.run(
        ["docker", "compose", *args], cwd=ROOT, check=check,
        capture_output=capture, text=True,
    )


def get(path):
    """GET em localhost:80 com o Host do site; devolve o cabeçalho X-Upstream."""
    req = urllib.request.Request("http://localhost" + path, headers={"Host": "frontend.test"})
    with urllib.request.urlopen(req, timeout=30) as r:
        r.read()
        return r.headers.get("X-Upstream")


def instancias_saudaveis():
    out = compose("ps", "wordpress", "--format", "json", capture=True).stdout
    linhas = [json.loads(l) for l in out.splitlines() if l.strip()]
    return sum(1 for c in linhas if c.get("Health") == "healthy")


def escalar(n):
    """Deixa exatamente n instâncias saudáveis e o nginx apontando para todas."""
    print(f"\n=== {n} instância(s) do WordPress ===", flush=True)
    compose("up", "-d", "--scale", f"wordpress={n}")
    for _ in range(60):
        if instancias_saudaveis() == n:
            break
        time.sleep(2)
    else:
        sys.exit(f"WordPress não ficou saudável com {n} instância(s)")
    # o nginx resolve o DNS só na inicialização: precisa reiniciar para enxergar as novas
    compose("restart", "nginx")
    for _ in range(30):
        try:
            get("/")
            break
        except Exception:
            time.sleep(1)
    upstreams = {get(POSTS[1]) for _ in range(6 * n)}
    if len(upstreams) != n:
        sys.exit(f"esperava {n} instância(s) recebendo tráfego, mas vi {sorted(upstreams)}")
    print(f"balanceamento ok: {sorted(upstreams)}", flush=True)


def resumo(prefixo):
    with open(RESULTS / f"{prefixo}_stats.csv", newline="") as f:
        agg = next(r for r in csv.DictReader(f) if r["Name"] == "Aggregated")
    return (f"{agg['Request Count']} req, {agg['Failure Count']} falhas, "
            f"média {float(agg['Average Response Time']):.0f} ms, "
            f"p95 {agg['95%']} ms, {float(agg['Requests/s']):.1f} req/s")


def rodar(cenario, usuarios, instancias, duracao, force):
    prefixo = f"c{cenario}_u{usuarios}_i{instancias}"
    if (RESULTS / f"{prefixo}_stats.csv").exists() and not force:
        print(f"[pula] {prefixo}: {resumo(prefixo)}", flush=True)
        return
    spawn_rate = max(1, usuarios // RAMP)
    print(f"[roda] {prefixo} ...", end=" ", flush=True)
    r = compose(
        "run", "--rm", "--no-deps", "-T", "-e", f"SCENARIO={cenario}", "locust",
        "--headless", "-u", str(usuarios), "-r", str(spawn_rate),
        "-t", f"{RAMP + duracao}s",
        "--reset-stats",   # zera as estatísticas quando todos os usuários já subiram
        "--csv", f"/mnt/results/{prefixo}", "--only-summary",
        capture=True, check=False,
    )
    if not (RESULTS / f"{prefixo}_stats.csv").exists():
        sys.exit(f"\nLocust não gerou o CSV (código {r.returncode}):\n{r.stderr[-1500:]}")
    print(resumo(prefixo), flush=True)


def main():
    ap = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    ap.add_argument("--users", type=int, nargs="+", default=USERS)
    ap.add_argument("--instances", type=int, nargs="+", default=INSTANCES)
    ap.add_argument("--scenarios", type=int, nargs="+", default=SCENARIOS)
    ap.add_argument("--duration", type=int, default=DURATION, help="segundos medidos por execução")
    ap.add_argument("--force", action="store_true", help="refaz execuções que já têm CSV")
    args = ap.parse_args()

    RESULTS.mkdir(exist_ok=True)
    total = len(args.users) * len(args.instances) * len(args.scenarios)
    print(f"{total} execuções de ~{RAMP + args.duration}s cada")
    try:
        for n in args.instances:
            escalar(n)
            for c in args.scenarios:
                for u in args.users:
                    rodar(c, u, n, args.duration, args.force)
    finally:
        print(f"\nvoltando para {DEFAULT_INSTANCES} instâncias ...")
        compose("up", "-d", "--scale", f"wordpress={DEFAULT_INSTANCES}", check=False)
        compose("restart", "nginx", check=False)


if __name__ == "__main__":
    main()
