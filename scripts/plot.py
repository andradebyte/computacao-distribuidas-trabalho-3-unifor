#!/usr/bin/env python3
"""Gera os gráficos a partir dos CSVs do Locust em results/.

Para cada cenário e métrica, uma figura com dois painéis:
  esquerda: usuários no eixo X, uma linha por nº de instâncias
  direita:  instâncias no eixo X, uma linha por nº de usuários

Uso: .venv/bin/python scripts/plot.py [--results results] [--out graficos]
"""
import argparse
import csv
import re
from collections import defaultdict
from pathlib import Path

import matplotlib

matplotlib.use("Agg")
import matplotlib.pyplot as plt  # noqa: E402

ROOT = Path(__file__).resolve().parent.parent
NOME = re.compile(r"c(\d+)_u(\d+)_i(\d+)_stats\.csv")

CENARIOS = {1: "imagem de 1 MB", 2: "texto de 400 KB", 3: "imagem de 300 KB"}

# nome do arquivo -> (título, rótulo do eixo Y, função que extrai o valor da linha Aggregated)
METRICAS = {
    "tempo_medio": ("Tempo médio de resposta", "ms", lambda r: float(r["Average Response Time"])),
    "p95": ("Tempo de resposta p95", "ms", lambda r: float(r["95%"])),
    "req_por_s": ("Requisições por segundo", "req/s", lambda r: float(r["Requests/s"])),
    "falhas": ("Taxa de falhas", "%",
               lambda r: 100 * int(r["Failure Count"]) / max(1, int(r["Request Count"]))),
}


def carregar(pasta):
    """dados[cenario][(usuarios, instancias)] = linha 'Aggregated' do CSV."""
    dados = defaultdict(dict)
    for arq in sorted(pasta.glob("c*_stats.csv")):
        m = NOME.fullmatch(arq.name)
        if not m:
            continue
        cenario, usuarios, instancias = map(int, m.groups())
        with open(arq, newline="") as f:
            agg = next((r for r in csv.DictReader(f) if r["Name"] == "Aggregated"), None)
        if agg:
            dados[cenario][(usuarios, instancias)] = agg
    return dados


def figura(cenario, chave, pontos, saida):
    titulo, unidade, valor = METRICAS[chave]
    usuarios = sorted({u for u, _ in pontos})
    instancias = sorted({i for _, i in pontos})

    fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(11, 4.2), sharey=True)

    for i in instancias:
        xs = [u for u in usuarios if (u, i) in pontos]
        ax1.plot(xs, [valor(pontos[(u, i)]) for u in xs], marker="o",
                 label=f"{i} instância{'s' if i > 1 else ''}")
    ax1.set_xscale("log")
    ax1.set_xticks(usuarios, [str(u) for u in usuarios])
    ax1.minorticks_off()
    ax1.set_xlabel("Usuários simultâneos")
    ax1.set_ylabel(unidade)
    ax1.legend()

    for u in usuarios:
        xs = [i for i in instancias if (u, i) in pontos]
        ax2.plot(xs, [valor(pontos[(u, i)]) for i in xs], marker="o", label=f"{u} usuários")
    ax2.set_xticks(instancias)
    ax2.set_xlabel("Instâncias do WordPress")
    ax2.legend()

    for ax in (ax1, ax2):
        ax.grid(alpha=0.3)
        ax.set_ylim(bottom=0)
    fig.suptitle(f"Cenário {cenario} ({CENARIOS.get(cenario, '')}) – {titulo}")
    fig.tight_layout()
    fig.savefig(saida, dpi=150)
    plt.close(fig)


PERCENTIS = ["50%", "66%", "75%", "80%", "90%", "95%", "98%", "99%", "99.9%", "99.99%", "100%"]


def figura_percentis(cenario, pontos, saida):
    """Distribuição de percentis: um painel por nº de usuários, uma linha por nº de instâncias."""
    usuarios = sorted({u for u, _ in pontos})
    instancias = sorted({i for _, i in pontos})
    fig, axes = plt.subplots(1, len(usuarios), figsize=(4.6 * len(usuarios), 4.4))
    for ax, u in zip(axes, usuarios):
        for i in instancias:
            if (u, i) in pontos:
                ax.plot(range(len(PERCENTIS)), [float(pontos[(u, i)][p]) for p in PERCENTIS],
                        marker="o", markersize=3, label=f"{i} instância{'s' if i > 1 else ''}")
        ax.set_xticks(range(len(PERCENTIS)), PERCENTIS, rotation=45)
        ax.set_title(f"{u} usuários")
        ax.set_xlabel("Percentil")
        ax.set_ylim(bottom=0)
        ax.grid(alpha=0.3)
    axes[0].set_ylabel("Tempo de resposta (ms)")
    axes[-1].legend()
    fig.suptitle(f"Cenário {cenario} ({CENARIOS.get(cenario, '')}) – distribuição de percentis do tempo de resposta")
    fig.tight_layout()
    fig.savefig(saida, dpi=150)
    plt.close(fig)


def main():
    ap = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    ap.add_argument("--results", type=Path, default=ROOT / "results")
    ap.add_argument("--out", type=Path, default=ROOT / "graficos")
    args = ap.parse_args()

    dados = carregar(args.results)
    if not dados:
        raise SystemExit(f"nenhum *_stats.csv em {args.results} (rode scripts/run_all.py antes)")
    args.out.mkdir(exist_ok=True)
    for cenario, pontos in sorted(dados.items()):
        for chave in METRICAS:
            saida = args.out / f"cenario{cenario}_{chave}.png"
            figura(cenario, chave, pontos, saida)
            print(saida.relative_to(ROOT) if saida.is_relative_to(ROOT) else saida)
        saida = args.out / f"cenario{cenario}_percentis.png"
        figura_percentis(cenario, pontos, saida)
        print(saida.relative_to(ROOT) if saida.is_relative_to(ROOT) else saida)


if __name__ == "__main__":
    main()
