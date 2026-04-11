# QA Smoke - ProjetoETC

Checklist mínimo para validação de regressão após mudanças.

## Pré-condições
1. Banco inicializado com bd.sql.
2. Pelo menos um usuário comum e um admin ativos.
3. Sessão limpa antes de iniciar casos de autenticação.

## Auth
1. Login válido: deve autenticar e redirecionar para painel.
2. Login inválido: deve exibir mensagem genérica de credenciais inválidas.
3. Logout: deve encerrar sessão apenas por POST.
4. Cadastro: deve validar campos obrigatórios e tamanho mínimo de senha.

## CSRF
1. Enviar POST sem _csrf_token: deve retornar erro de token inválido/expirado.
2. Enviar POST com token válido: deve processar normalmente.
3. Curtida/comentário via AJAX: deve funcionar com token válido.

## Painel
1. Criar denúncia com dados válidos: deve persistir e listar no painel.
2. Editar denúncia do próprio usuário: deve atualizar.
3. Tentar editar denúncia de outro usuário comum: deve bloquear.
4. Comentar denúncia: deve criar comentário.
5. Curtir denúncia/comentário: deve alternar estado e contador.

## Upload
1. Upload JPG/PNG válido <= 5MB: deve aceitar.
2. Upload com tipo não permitido: deve rejeitar.
3. Upload com dimensão maior que 4000x3000: deve rejeitar.

## Admin
1. Usuário comum acessando rota admin: deve ser bloqueado.
2. Admin cadastrando/atualizando/excluindo categoria: deve funcionar com CSRF.

## Performance básica
1. Listagem do painel com paginação e filtros deve responder sem travar.
2. Verificar tempos de carregamento em base com volume moderado de denúncias/comentários.
