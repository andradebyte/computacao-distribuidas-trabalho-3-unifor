import os
import re

from locust import HttpUser, between, task

# Cada cenário é um post criado por scripts/seed_posts.py.
POSTS = {
    "1": "/index.php/2026/01/01/cenario-1-imagem-1mb/",
    "2": "/index.php/2026/01/01/cenario-2-texto-400kb/",
    "3": "/index.php/2026/01/01/cenario-3-imagem-300kb/",
}

scenario = os.environ.get("SCENARIO")
if scenario not in POSTS:
    raise SystemExit(f"SCENARIO deve ser 1, 2 ou 3 (recebido: {scenario!r})")
POST = POSTS[scenario]

# imagem enviada pelo seed_posts.py, referenciada no HTML do post
IMAGEM = re.compile(r'src="https?://[^/"]+(/wp-content/uploads/[^"]+\.png)"')


class Visitante(HttpUser):
    # pausa de 1 a 3 s entre as ações de cada usuário simulado
    wait_time = between(1, 3)

    @task
    def le_post(self):
        resp = self.client.get(POST, name="post")
        html = resp.text if resp.ok else ""
        # o Locust não é um navegador: não baixa a imagem sozinho.
        # Pedimos o que está no HTML do post, como um navegador faria ao renderizá-lo.
        img = IMAGEM.search(html)
        if img:
            self.client.get(img.group(1), name="imagem")
