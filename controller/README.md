# Camada Controller

Esta pasta contém os controllers do projeto organizados por contexto de uso.

- **AuthController.php**: cadastro, login, logout, perfil e exclusão de conta.
- **PainelController.php**: ações do usuário autenticado no painel.
- **AdminController.php**: rotinas administrativas restritas.

## Responsabilidade

- Receber os dados da `View` ou da requisição roteada.
- Validar entrada, sessão e permissão quando necessário.
- Orquestrar chamadas aos DAOs e DTOs.
- Redirecionar para views ou rotas apropriadas.

## Regras

- Não deve conter SQL direto.
- Não deve conter HTML pesado.
- Rotas sensíveis devem validar autenticação e autorização no próprio fluxo do controller.