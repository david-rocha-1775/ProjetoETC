# DTO (Data Transfer Object)

Esta pasta contém objetos simples para transportar dados entre controller e DAO.

## Responsabilidade

- Representar os dados do sistema sem conter regras de negócio pesadas.
- Expor atributos por meio de `getters` e `setters`.
- Manter o contrato de dados previsível entre as camadas.

## O que colocar aqui

- Classes com atributos privados.
- Métodos de acesso e mutação.
- Estruturas que representam registros de domínio usados pela aplicação.