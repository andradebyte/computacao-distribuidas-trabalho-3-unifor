#!/usr/bin/env python3
"""Cria os 3 posts dos cenários de teste (idempotente: recria se já existirem).

  cenario-1-imagem-1mb   -> post com imagem de ~1MB
  cenario-2-texto-400kb  -> post com texto de ~400KB
  cenario-3-imagem-300kb -> post com imagem de ~300KB

Requer a stack no ar (docker compose up -d).
"""
import os
import struct
import subprocess
import tempfile
import zlib
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
DATA_POST = "2026-01-01 12:00:00"  # data fixa => permalink previsível (/index.php/2026/01/01/<slug>/)

PARAGRAFO = (
    "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor "
    "incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud "
    "exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n\n"
)


def noise_png(path, target_bytes):
    """PNG com pixels aleatórios: não comprime, então tamanho do arquivo ≈ target_bytes."""
    side = int((target_bytes / 3) ** 0.5)
    raw = b"".join(b"\x00" + os.urandom(side * 3) for _ in range(side))

    def chunk(tag, data):
        c = struct.pack(">I", len(data)) + tag + data
        return c + struct.pack(">I", zlib.crc32(tag + data) & 0xFFFFFFFF)

    png = (
        b"\x89PNG\r\n\x1a\n"
        + chunk(b"IHDR", struct.pack(">IIBBBBB", side, side, 8, 2, 0, 0, 0))
        + chunk(b"IDAT", zlib.compress(raw, 0))
        + chunk(b"IEND", b"")
    )
    path.write_bytes(png)


def texto_400kb(path):
    texto = (PARAGRAFO * (400_000 // len(PARAGRAFO) + 1))[:400_000]
    # <!--more--> faz a home mostrar só o 1º parágrafo; o post continua com os 400KB inteiros
    path.write_text(texto.replace("\n\n", "\n\n<!--more-->\n\n", 1))


def rede_do_compose():
    db = subprocess.run(
        ["docker", "compose", "ps", "-q", "db"],
        cwd=ROOT, capture_output=True, text=True, check=True,
    ).stdout.split()[0]
    return subprocess.run(
        ["docker", "inspect", "-f",
         "{{range $k,$v := .NetworkSettings.Networks}}{{$k}}{{end}}", db],
        capture_output=True, text=True, check=True,
    ).stdout.strip()


def main():
    with tempfile.TemporaryDirectory() as assets:
        assets = Path(assets)
        noise_png(assets / "imagem-1mb.png", 1_000_000)
        noise_png(assets / "imagem-300kb.png", 300_000)
        texto_400kb(assets / "texto-400kb.txt")
        rede = rede_do_compose()

        def wp(*args):
            """Roda o WP-CLI num contêiner descartável e devolve o stdout."""
            r = subprocess.run(
                ["docker", "run", "--rm", "--user", "33:33", "--network", rede,
                 "-e", "WORDPRESS_DB_HOST=db", "-e", "WORDPRESS_DB_USER=example",
                 "-e", "WORDPRESS_DB_PASSWORD=example", "-e", "WORDPRESS_DB_NAME=example",
                 "-v", f"{ROOT / 'data/wordpress'}:/var/www/html",
                 "-v", f"{assets}:/assets:ro",
                 "wordpress:cli", *args],
                capture_output=True, text=True,
            )
            if r.returncode != 0:
                raise SystemExit(f"wp {' '.join(args)} falhou:\n{r.stderr}")
            return r.stdout.strip()

        # Comentários aparecem na hora (por padrão o 1º comentário de cada autor fica pendente)
        wp("option", "update", "comment_whitelist", "0")
        wp("option", "update", "comment_moderation", "0")

        # Remove posts antigos dos cenários, se houver
        for slug in ("cenario-1-imagem-1mb", "cenario-2-texto-400kb", "cenario-3-imagem-300kb"):
            ids = wp("post", "list", "--post_type=post", f"--name={slug}", "--format=ids")
            if ids:
                wp("post", "delete", *ids.split(), "--force")

        # Remove também as imagens antigas (o WordPress renomearia a nova para imagem-1mb-1.png)
        anexos = wp("post", "list", "--post_type=attachment", "--s=imagem-", "--format=ids")
        if anexos:
            wp("post", "delete", *anexos.split(), "--force")

        def post_com_imagem(titulo, slug, arquivo, descricao):
            img = wp("media", "import", f"/assets/{arquivo}", "--porcelain")
            url = wp("post", "get", img, "--field=guid")
            return wp(
                "post", "create", f"--post_title={titulo}", f"--post_name={slug}",
                "--post_status=publish", f"--post_date={DATA_POST}", "--porcelain",
                f'--post_content=<p>{descricao}</p><img src="{url}" />',
            )

        p1 = post_com_imagem("Cenário 1 - imagem 1MB", "cenario-1-imagem-1mb",
                             "imagem-1mb.png", "Post com imagem de ~1MB.")
        p2 = wp("post", "create", "/assets/texto-400kb.txt",
                "--post_title=Cenário 2 - texto 400KB", "--post_name=cenario-2-texto-400kb",
                "--post_status=publish", f"--post_date={DATA_POST}", "--porcelain")
        p3 = post_com_imagem("Cenário 3 - imagem 300KB", "cenario-3-imagem-300kb",
                             "imagem-300kb.png", "Post com imagem de ~300KB.")

        print(f"posts criados: {p1} {p2} {p3}")
        print(wp("post", "list", "--post_type=post", "--fields=ID,post_name,url"))


if __name__ == "__main__":
    main()
