# Camada Model

Esta pasta concentra a estrutura de dados da aplicação.

## Estrutura interna

- `dao/`: classes de acesso ao banco de dados.
- `dto/`: objetos de transferência de dados entre controller e DAO.

## Responsabilidade

- Encapsular consultas e persistência.
- Representar dados de forma consistente entre as camadas.
- Evitar mistura de lógica de apresentação com lógica de dados.

## Observações

- O modelo deve manter contratos claros entre DAO e DTO.
- Operações que alteram mais de uma tabela precisam tratar integridade e falhas de forma explícita.