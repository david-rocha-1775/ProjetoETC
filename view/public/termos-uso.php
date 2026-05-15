<div class="termos-uso-container">
    <div class="termos-uso-header">
        <h2 class="h4 fw-bold mb-2">Termos de Uso e Política de Privacidade</h2>
        <p class="text-body-secondary small">
            <strong>Versão 1.0</strong> — Última atualização: <?= date('d/m/Y') ?><br>
            Sistema: Cidade Atenta
        </p>
    </div>

    <hr class="my-3">

    <!-- Introdução -->
    <section class="mb-4">
        <h3 class="h5 fw-bold mb-2">Introdução</h3>
        <p>
            Bem-vindo ao <strong>Cidade Atenta</strong>, uma plataforma colaborativa de participação cidadã que visa
            fortalecer a comunicação entre cidadãos e a administração pública. Ao utilizar nosso sistema, você concorda
            com estes Termos de Uso e com a Política de Privacidade descrita abaixo.
        </p>
        <p>
            Este documento foi elaborado em conformidade com a <strong>Lei Geral de Proteção de Dados (LGPD —
                Lei 13.709/2018)</strong> e outras legislações brasileiras aplicáveis.
        </p>
    </section>

    <!-- Seção 1: Dados Coletados -->
    <section class="mb-4">
        <h3 class="h5 fw-bold mb-2">1. Dados Coletados e Finalidade</h3>
        <p>
            Coletamos as seguintes informações durante o processo de cadastro e uso da plataforma:
        </p>
        <ul class="ps-3">
            <li><strong>Nome Completo</strong> — para identificação e comunicação com você</li>
            <li><strong>Endereço de E-mail</strong> — para autenticação, recuperação de senha e notificações</li>
            <li><strong>Senha (armazenada em hash criptográfico)</strong> — para segurança da sua conta</li>
            <li><strong>Endereço IP</strong> — para registros de segurança e auditoria</li>
            <li><strong>Data e Hora de Acesso</strong> — para análise de uso e conformidade</li>
            <li><strong>Denúncias, Comentários e Interações</strong> — conteúdo que você publica na plataforma</li>
            <li><strong>Localização Aproximada (via mapa)</strong> — para contextualizar denúncias geográficas</li>
        </ul>
        <p class="mt-3">
            <strong>Finalidade:</strong> Estas informações são utilizadas para:
        </p>
        <ul class="ps-3">
            <li>Permitir o funcionamento da plataforma e acesso à sua conta</li>
            <li>Enviar notificações sobre denúncias, comentários e atualizações</li>
            <li>Melhorar a segurança e prevenir fraudes ou abusos</li>
            <li>Conformidade com obrigações legais</li>
            <li>Análise agregada de uso para melhorias de serviço</li>
        </ul>
    </section>

    <!-- Seção 2: Direitos do Usuário (LGPD art.18) -->
    <section class="mb-4">
        <h3 class="h5 fw-bold mb-2">2. Seus Direitos (LGPD — Artigo 18)</h3>
        <p>
            De acordo com a Lei Geral de Proteção de Dados, você possui os seguintes direitos sobre seus dados:
        </p>
        <ul class="ps-3">
            <li>
                <strong>Direito de Acesso:</strong> Solicitar informações sobre quais dados pessoais temos sobre você
            </li>
            <li>
                <strong>Direito de Retificação:</strong> Corrigir dados inexatos ou incompletos
            </li>
            <li>
                <strong>Direito de Exclusão:</strong> Solicitar a exclusão de seus dados (direito ao esquecimento),
                observadas as limitações legais
            </li>
            <li>
                <strong>Direito de Portabilidade:</strong> Receber seus dados em formato estruturado e portável
            </li>
            <li>
                <strong>Direito de Oposição:</strong> Opor-se ao tratamento de seus dados em certas situações
            </li>
            <li>
                <strong>Direito de Não Ser Discriminado:</strong> Não sofrer discriminação por exercer seus direitos
                LGPD
            </li>
        </ul>
        <p class="mt-3">
            Para exercer estes direitos, entre em contato conosco através do endereço de e-mail fornecido na seção de
            Contato.
        </p>
    </section>

    <!-- Seção 3: Retenção de Dados -->
    <section class="mb-4">
        <h3 class="h5 fw-bold mb-2">3. Retenção e Exclusão de Dados</h3>
        <p>
            Seus dados pessoais serão armazenados pelo tempo necessário para:
        </p>
        <ul class="ps-3">
            <li>Manter sua conta ativa e fornecer os serviços da plataforma</li>
            <li>Cumprir obrigações legais, regulatórias ou de segurança pública</li>
            <li>Resolver disputas ou investigações de abuso</li>
        </ul>
        <p class="mt-3">
            <strong>Prazos específicos:</strong>
        </p>
        <ul class="ps-3">
            <li>
                <strong>Dados de Conta (nome, e-mail, senha):</strong> Armazenados enquanto a conta está ativa;
                deletados
                em até 90 dias após exclusão da conta
            </li>
            <li>
                <strong>Logs de Acesso (IP, timestamps):</strong> Retidos por até 12 meses para fins de segurança
            </li>
            <li>
                <strong>Denúncias Públicas:</strong> Mantidas conforme política de uso da plataforma, podendo ser
                arquivadas após 3 anos de inatividade
            </li>
        </ul>
    </section>

    <!-- Seção 4: Proteção de Dados -->
    <section class="mb-4">
        <h3 class="h5 fw-bold mb-2">4. Proteção de Dados e Segurança</h3>
        <p>
            Implementamos medidas técnicas e organizacionais para proteger seus dados:
        </p>
        <ul class="ps-3">
            <li>
                <strong>Criptografia em Trânsito:</strong> Comunicação via HTTPS/TLS para proteger dados em transmissão
            </li>
            <li>
                <strong>Hashing de Senha:</strong> Senhas são armazenadas como hash criptográfico, nunca em texto plano
            </li>
            <li>
                <strong>Prepared Statements:</strong> Proteção contra injeção SQL em todas as consultas ao banco de
                dados
            </li>
            <li>
                <strong>Validação de Entrada:</strong> Verificação rigorosa de dados antes do armazenamento
            </li>
            <li>
                <strong>Controle de Acesso:</strong> Apenas pessoal autorizado acessa dados sensíveis
            </li>
            <li>
                <strong>Auditorias Regulares:</strong> Avaliação contínua de segurança e conformidade
            </li>
        </ul>
        <p class="mt-3">
            Apesar de nossas medidas, nenhum sistema é completamente seguro. Comunicamos qualquer violação de dados
            conforme exigido pela LGPD em até 72 horas.
        </p>
    </section>

    <!-- Seção 5: Termos de Uso Geral -->
    <section class="mb-4">
        <h3 class="h5 fw-bold mb-2">5. Termos de Uso Gerais</h3>
        <p>
            Ao utilizar o Cidade Atenta, você concorda em:
        </p>
        <ul class="ps-3">
            <li>Fornecer informações precisas e manter seus dados atualizados</li>
            <li>
                Ser responsável pela confidencialidade de suas credenciais de acesso (e-mail e senha)
            </li>
            <li>
                Não utilizar a plataforma para atividades ilegais, fraudulentas ou prejudiciais
            </li>
            <li>
                Não fazer upload de conteúdo ofensivo, discriminatório ou que viole direitos de terceiros
            </li>
            <li>
                Respeitar a privacidade e direitos de outros usuários
            </li>
            <li>
                Notificar-nos imediatamente sobre qualquer uso não autorizado de sua conta
            </li>
        </ul>
    </section>

    <!-- Seção 6: Limitações de Responsabilidade -->
    <section class="mb-4">
        <h3 class="h5 fw-bold mb-2">6. Limitações de Responsabilidade</h3>
        <p>
            A plataforma é fornecida "no estado em que se encontra". Não garantimos:
        </p>
        <ul class="ps-3">
            <li>Disponibilidade 24/7 contínua do serviço</li>
            <li>Ausência de erros ou interrupções</li>
            <li>Que o conteúdo de denúncias será sempre verificado ou investigado</li>
        </ul>
        <p class="mt-3">
            O administrador não será responsável por danos diretos, indiretos ou consequentes resultantes do uso da
            plataforma.
        </p>
    </section>

    <!-- Seção 7: Modificações dos Termos -->
    <section class="mb-4">
        <h3 class="h5 fw-bold mb-2">7. Modificações dos Termos</h3>
        <p>
            Reservamos o direito de modificar estes Termos de Uso e Política de Privacidade a qualquer momento.
            Modificações significativas serão notificadas por e-mail. O uso continuado da plataforma após as
            modificações constitui aceitação dos novos termos.
        </p>
    </section>

    <!-- Rodapé -->
    <hr class="my-3">
    <footer class="termos-uso-footer text-body-secondary small">
        <p class="mb-1">
            <strong>Última Atualização:</strong> <?= date('d/m/Y') ?>
        </p>
        <p class="mb-0">
            Este documento está em conformidade com a Lei Geral de Proteção de Dados (LGPD — Lei 13.709/2018) e
            reflete o compromisso do Cidade Atenta com a privacidade e segurança de dados dos usuários.
        </p>
    </footer>
</div>

<style>
    .termos-uso-container {
        max-height: 500px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .termos-uso-container ul {
        margin-bottom: 0.75rem;
    }

    .termos-uso-container li {
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }

    .termos-uso-container section {
        scroll-margin-top: 20px;
    }

    /* Scrollbar customizado (opcional, para navegadores modernos) */
    .termos-uso-container::-webkit-scrollbar {
        width: 6px;
    }

    .termos-uso-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .termos-uso-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .termos-uso-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>