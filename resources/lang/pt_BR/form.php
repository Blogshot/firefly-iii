<?php

/**
 * form.php
 * Copyright (c) 2019 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

return [
    // new user:
    'bank_name'                   => 'Nome do banco',
    'bank_balance'                => 'Saldo',
    'savings_balance'             => 'Salda da Poupança',
    'credit_card_limit'           => 'Limite do Cartão de Crédito',
    'automatch'                   => 'Equivale automaticamente',
    'skip'                        => 'Pular',
    'enabled'                     => 'Habilitado',
    'name'                        => 'Nome',
    'active'                      => 'Ativar',
    'amount_min'                  => 'Valor Mínimo',
    'amount_max'                  => 'Valor Máximo',
    'match'                       => 'Corresponde em',
    'strict'                      => 'Modo estrito',
    'repeat_freq'                 => 'Repetições',
    'object_group'                => 'Grupo',
    'location'                    => 'Localização',
    'update_channel'              => 'Atualizar canal',
    'currency_id'                 => 'Moeda',
    'transaction_currency_id'     => 'Moeda',
    'auto_budget_currency_id'     => 'Moeda',
    'external_ip'                 => 'O IP externo do seu servidor',
    'attachments'                 => 'Anexos',
    'BIC'                         => 'BIC',
    'verify_password'             => 'Verificação da segurança da senha',
    'source_account'              => 'Conta de origem',
    'destination_account'         => 'Conta de destino',
    'asset_destination_account'   => 'Conta de destino',
    'include_net_worth'           => 'Incluir no patrimonio liquido',
    'asset_source_account'        => 'Conta de origem',
    'journal_description'         => 'Descrição',
    'note'                        => 'Notas',
    'currency'                    => 'Moeda',
    'account_id'                  => 'Conta de ativo',
    'budget_id'                   => 'Orçamento',
    'bill_id'                     => 'Conta',
    'opening_balance'             => 'Saldo inicial',
    'tagMode'                     => 'Modo de tag',
    'virtual_balance'             => 'Saldo virtual',
    'targetamount'                => 'Valor alvo',
    'account_role'                => 'Função de conta',
    'opening_balance_date'        => 'Data do saldo inicial',
    'cc_type'                     => 'Plano de pagamento do Cartão de Crédito',
    'cc_monthly_payment_date'     => 'Data do pagamento mensal do Cartão de Crédito',
    'piggy_bank_id'               => 'Cofrinho',
    'returnHere'                  => 'Retornar aqui',
    'returnHereExplanation'       => 'Depois de armazenar, retorne aqui para criar outro.',
    'returnHereUpdateExplanation' => 'Depois da atualização, retorne aqui',
    'description'                 => 'Descrição',
    'expense_account'             => 'Conta de Despesa',
    'revenue_account'             => 'Conta de Receita',
    'decimal_places'              => 'Casas décimais',
    'destination_amount'          => 'Quantidade (destino)',
    'new_email_address'           => 'Novo endereço de e-mail',
    'verification'                => 'Verificação',
    'api_key'                     => 'Chave da API',
    'remember_me'                 => 'Lembrar-me',
    'liability_type_id'           => 'Tipo de passivo',
    'liability_type'              => 'Tipo de passivo',
    'interest'                    => 'Juros',
    'interest_period'             => 'Período de juros',
    'extension_date'              => 'Data de renovação',
    'type'                        => 'Tipo',
    'convert_Withdrawal'          => 'Converter a retirada',
    'convert_Deposit'             => 'Converter o depósito',
    'convert_Transfer'            => 'Converter a transferência',
    'amount'                      => 'Valor',
    'foreign_amount'              => 'Montante em moeda estrangeira',
    'date'                        => 'Data',
    'interest_date'               => 'Data de interesse',
    'book_date'                   => 'Data reserva',
    'process_date'                => 'Data de processamento',
    'category'                    => 'Categoria',
    'tags'                        => 'Etiquetas',
    'deletePermanently'           => 'Apagar permanentemente',
    'cancel'                      => 'Cancelar',
    'targetdate'                  => 'Data Alvo',
    'startdate'                   => 'Data de Início',
    'tag'                         => 'Etiqueta',
    'under'                       => 'Debaixo',
    'symbol'                      => 'Símbolo',
    'code'                        => 'Código',
    'iban'                        => 'IBAN',
    'account_number'              => 'Número de conta',
    'creditCardNumber'            => 'Número do cartão de crédito',
    'has_headers'                 => 'Cabeçalhos',
    'date_format'                 => 'Formato da Data',
    'specifix'                    => 'Banco- ou arquivo específico corrigídos',
    'attachments[]'               => 'Anexos',
    'title'                       => 'Título',
    'notes'                       => 'Notas',
    'filename'                    => 'Nome do arquivo',
    'mime'                        => 'Tipo do Arquivo (MIME)',
    'size'                        => 'Tamanho',
    'trigger'                     => 'Disparo',
    'stop_processing'             => 'Parar processamento',
    'start_date'                  => 'Início do intervalo',
    'end_date'                    => 'Final do intervalo',
    'enddate'                     => 'Data final',
    'start'                       => 'Início do intervalo',
    'end'                         => 'Término do intervalo',
    'delete_account'              => 'Apagar conta ":name"',
    'delete_webhook'              => 'Excluir webhook ":title"',
    'delete_bill'                 => 'Apagar fatura ":name"',
    'delete_budget'               => 'Excluir o orçamento ":name"',
    'delete_category'             => 'Excluir categoria ":name"',
    'delete_currency'             => 'Excluir moeda ":name"',
    'delete_journal'              => 'Excluir a transação com a descrição ":description"',
    'delete_attachment'           => 'Apagar anexo ":name"',
    'delete_rule'                 => 'Excluir regra ":title"',
    'delete_rule_group'           => 'Exclua o grupo de regras ":title"',
    'delete_link_type'            => 'Excluir tipo de link ":name"',
    'delete_user'                 => 'Excluir o usuário ":email"',
    'delete_recurring'            => 'Apagar transação recorrente ":title"',
    'user_areYouSure'             => 'Se você excluir o usuário ":email", tudo desaparecerá. Não será possível desfazer a ação. Se excluir você mesmo, você perderá acesso total a essa instância do Firefly III.',
    'attachment_areYouSure'       => 'Tem certeza que deseja excluir o anexo denominado ":name"?',
    'account_areYouSure'          => 'Tem certeza que deseja excluir a conta denominada ":name"?',
    'account_areYouSure_js'       => 'Tem certeza que deseja excluir a conta "{name}"?',
    'bill_areYouSure'             => 'Você tem certeza que quer apagar a fatura ":name"?',
    'rule_areYouSure'             => 'Tem certeza que deseja excluir a regra intitulada ":title"?',
    'object_group_areYouSure'     => 'Você tem certeza que deseja excluir a regra intitulada ":title"?',
    'ruleGroup_areYouSure'        => 'Tem certeza que deseja excluir o grupo de regras intitulado ":title"?',
    'budget_areYouSure'           => 'Tem certeza que deseja excluir o orçamento chamado ":name"?',
    'webhook_areYouSure'           => 'Você tem certeza que quer excluir o webhook chamado ":title"?',
    'category_areYouSure'         => 'Tem certeza que deseja excluir a categoria com o nome ":name"?',
    'recurring_areYouSure'        => 'Tem certeza que deseja excluir o grupo de regras intitulado ":title"?',
    'currency_areYouSure'         => 'Tem certeza que deseja excluir a moeda chamada ":name"?',
    'piggyBank_areYouSure'        => 'Tem certeza que deseja excluir o cofrinho chamado ":name"?',
    'journal_areYouSure'          => 'Tem certeza que deseja excluir a transação descrita ":description"?',
    'mass_journal_are_you_sure'   => 'Tem a certeza que pretende apagar estas transações?',
    'tag_areYouSure'              => 'Você tem certeza que quer apagar a tag ":tag"?',
    'journal_link_areYouSure'     => 'Tem certeza que deseja excluir a ligação entre <a href=":source_link">:source</a> e <a href=":destination_link">:destination</a>?',
    'linkType_areYouSure'         => 'Tem certeza que deseja excluir o tipo de link ":name" (":inward" / ":outward")?',
    'permDeleteWarning'           => 'Exclusão de dados do Firefly III são permanentes e não podem ser desfeitos.',
    'mass_make_selection'         => 'Você ainda pode evitar que itens sejam excluídos, removendo a caixa de seleção.',
    'delete_all_permanently'      => 'Exclua os selecionados permanentemente',
    'update_all_journals'         => 'Atualizar essas transações',
    'also_delete_transactions'    => 'A única transação ligada a essa conta será excluída também.|Todas as :count transações ligadas a esta conta serão excluídas também.',
    'also_delete_transactions_js' => 'Sem transações|A única transação conectada a esta conta também será excluída.|Todas as {count} transações conectadas a essa conta também serão excluídas.',
    'also_delete_connections'     => 'A única transação relacionada com este tipo de link vai perder a conexão. | Todas as transações de :count ligadas com este tipo de link vão perder sua conexão.',
    'also_delete_rules'           => 'A única regra que ligado a este grupo de regras será excluída também.|Todos as :count regras ligadas a este grupo de regras serão excluídas também.',
    'also_delete_piggyBanks'      => 'O único cofrinho conectado a essa conta será excluído também.|Todos os :count cofrinhos conectados a esta conta serão excluídos também.',
    'also_delete_piggyBanks_js'   => 'Sem cofrinhos|O único cofrinho conectado a esta conta também será excluído.|Todos os {count} cofrinhos conectados a esta conta também serão excluídos.',
    'not_delete_piggy_banks'      => 'O cofrinho conectado a este grupo não será excluído.|Os :count cofrinhos conectados a este grupo não serão excluídos.',
    'bill_keep_transactions'      => 'A única transação conectada a esta conta não será excluída.|Todas as :count transações conectadas a esta conta não serão excluídas.',
    'budget_keep_transactions'    => 'A única transação conectada a este orçamento não será excluída.|Todas as :count transações conectadas a este orçamento não serão excluídas.',
    'category_keep_transactions'  => 'A única transação conectada a esta categoria não será excluída.|Todas as :count transações conectadas a esta categoria não serão excluídas.',
    'recurring_keep_transactions' => 'A única transação criada por esta transação recorrente não será excluída.|Todas as :count transações criadas por esta transação recorrente não serão excluídas.',
    'tag_keep_transactions'       => 'A única transação conectada a esta tag não será excluída.|Todas as :count transações conectadas a esta tag não serão excluídas.',
    'check_for_updates'           => 'Buscar atualizações',
    'liability_direction'         => 'Passivo entrada/saída',
    'delete_object_group'         => 'Excluir grupo ":title"',
    'email'                       => 'E-mail',
    'password'                    => 'Senha',
    'password_confirmation'       => 'Senha(Confirmar)',
    'blocked'                     => 'Está bloqueado?',
    'blocked_code'                => 'Razão para ser reportado',
    'login_name'                  => 'Login',
    'is_owner'                    => 'É administrador(a)?',
    'url'                         => 'link',
    'bill_end_date'               => 'Data final',

    // import
    'apply_rules'                 => 'Aplicar Regras',
    'artist'                      => 'Artista',
    'album'                       => 'Álbum',
    'song'                        => 'Música',


    // admin
    'domain'                      => 'Domínio',
    'single_user_mode'            => 'Desabilitar registro de usuários',
    'is_demo_site'                => 'É o site de demonstração',

    // import
    'configuration_file'          => 'Arquivo de configuração',
    'csv_comma'                   => 'Uma vírgula (,)',
    'csv_semicolon'               => 'Um ponto e vírgula (;)',
    'csv_tab'                     => 'Um Tab (invisível)',
    'csv_delimiter'               => 'Delimitador de campo CSV',
    'client_id'                   => 'Id do Cliente',
    'app_id'                      => 'App ID',
    'secret'                      => 'Segredo',
    'public_key'                  => 'Chave pública',
    'country_code'                => 'Código do país',
    'provider_code'               => 'Banco ou provedor de dados',
    'fints_url'                   => 'URL da API do FinTS',
    'fints_port'                  => 'Porta',
    'fints_bank_code'             => 'Código do banco',
    'fints_username'              => 'Usuário',
    'fints_password'              => 'PIN / Senha',
    'fints_account'               => 'Conta no FinTS',
    'local_account'               => 'Conta no Firefly III',
    'from_date'                   => 'Data inicial',
    'to_date'                     => 'Data final',
    'due_date'                    => 'Data de vencimento',
    'payment_date'                => 'Data de pagamento',
    'invoice_date'                => 'Data da Fatura',
    'internal_reference'          => 'Referência interna',
    'inward'                      => 'Descrição interna',
    'outward'                     => 'Descrição externa',
    'rule_group_id'               => 'Grupo de regras',
    'transaction_description'     => 'Descrição da transação',
    'first_date'                  => 'Primeira data',
    'transaction_type'            => 'Tipo de transação',
    'repeat_until'                => 'Repetir até',
    'recurring_description'       => 'Descrição da transação recorrente',
    'repetition_type'             => 'Tipo de repetição',
    'foreign_currency_id'         => 'Moeda estrangeira',
    'repetition_end'              => 'Repetição termina',
    'repetitions'                 => 'Repetições',
    'calendar'                    => 'Calendário',
    'weekend'                     => 'Fim de Semana',
    'client_secret'               => 'Chave secreta',
    'withdrawal_destination_id'   => 'Conta de destino',
    'deposit_source_id'           => 'Conta de origem',
    'expected_on'                 => 'Esperado em',
    'paid'                        => 'Pago',
    'auto_budget_type'            => 'Orçamento automático',
    'auto_budget_amount'          => 'Valor do orçamento automático',
    'auto_budget_period'          => 'Período de orçamento automático',
    'collected'                   => 'Coletado',
    'submitted'                   => 'Enviado',
    'key'                         => 'Chave',
    'value'                       => 'Conteúdo do registro',
    'webhook_delivery'            => 'Entrega',
    'webhook_response'            => 'Resposta',
    'webhook_trigger'             => 'Gatilho',
];
