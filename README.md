# ProjetoETC

Plataforma web para registro e acompanhamento de denúncias urbanas.

## Stack oficial
- PHP 8.5+
- MySQL/InnoDB com PDO
- Arquitetura MVC custom com front controller
- Bootstrap 5 local + JavaScript vanilla + Leaflet local

## Requisitos
- PHP 8.5 ou superior
- MySQL 8+
- Apache (XAMPP recomendado no ambiente local)

## Como executar localmente
1. Configure o virtual host/pasta pública apontando para a raiz do projeto.
2. Crie o banco e tabelas usando o arquivo [bd.sql](bd.sql).
3. Ajuste credenciais em [config/database.php](config/database.php).
4. Acesse a aplicação por [index.php](index.php).

## Arquitetura
- [index.php](index.php): front controller e despacho de rotas.
- [config/routes/auth.php](config/routes/auth.php), [config/routes/painel.php](config/routes/painel.php), [config/routes/admin.php](config/routes/admin.php), [config/routes/public.php](config/routes/public.php): whitelist de rotas.
- [controller](controller): coordenação de fluxo e regras de aplicação.
- [model/dao](model/dao): acesso a dados com prepared statements.
- [model/dto](model/dto): objetos de transporte de dados.
- [view](view): renderização HTML e templates.

## Segurança implementada
- Validação de método HTTP por rota.
- Proteção CSRF para ações POST.
- Sessão regenerada em login/logout/exclusão de conta.
- Escape de saída em pontos sensíveis de template.
- Upload de imagem com validação de extensão, MIME, tipo real e dimensões.

## Documentação complementar
- [docs/README.md](docs/README.md)
- [docs/SEGURANCA.md](docs/SEGURANCA.md)
- [docs/QA-SMOKE.md](docs/QA-SMOKE.md)
