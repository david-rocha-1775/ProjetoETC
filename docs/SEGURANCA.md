# Segurança - ProjetoETC

## Baseline
- PHP 8.5+
- Sessão iniciada no front controller
- Rotas com whitelist por arquivo em config/routes

## Controles atuais
1. CSRF obrigatório para rotas POST do tipo action.
2. Token CSRF renderizado em formulários com helper compartilhado.
3. Token CSRF aceito por campo de formulário e header X-CSRF-Token.
4. Sessão regenerada após login, logout e exclusão de conta.
5. Logout somente por POST.
6. Mensagem de login unificada para credenciais inválidas.
7. Upload de imagem validado por extensão, MIME, tipo real (exif_imagetype) e dimensões máximas.
8. Bloqueio de execução de scripts em uploads via .htaccess.

## Regras para novas features
1. Toda rota POST deve usar CSRF.
2. Toda saída dinâmica em HTML deve usar escaping.
3. Controller não deve executar SQL diretamente.
4. Toda consulta deve usar prepared statements no DAO.
5. Campos de entrada devem ter validação de tamanho/formato no backend.

## Riscos residuais recomendados para próxima fase
1. Rate limiting para login.
2. Logs estruturados para auditoria de segurança.
3. Política de headers HTTP de segurança no front controller.
