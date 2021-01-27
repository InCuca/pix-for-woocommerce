=== Pix for WooCommerce ===
Contributors: incuca, samoaste, dejean-echeverrya, rafengineer, paulodanieldiasdilva, dionmaicon
Tags: woocommerce, payment gateway, gateway, pix
Requires WooCommerce at least: 2.1
Tested up to: 5.6
Requires PHP: 7.1
Stable Tag: 1.2.0
License: GPLv3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adiciona Pix como método de pagamento no WooCommerce.

== Description ==

Plugin WooCommerce para receber seus pagamentos via [PIX](https://www.bb.com.br/pbb/pagina-inicial/pix#/).

O que esse plugin faz?

- Adiciona um Gateway de pagamento para o WooCommerce.
- Facilita e  agiliza seus pagamentos eliminando um intermediário.
- Permite ao cliente envio de comprovantes via WhatsApp, Telegram ou E-mail.

Veja como é fácil fazer suas vendas com o PIX:

1. Você instala o Plugin.
2. Cadastra sua chave PIX.
3. O cliente finaliza a compra informando o PIX como meio de pagamento.
4. O cliente efetua o pagamento e envia o comprovante.
5. Você conclui a venda no Painel de administração do WooCommerce.
6. Envia o produto ao cliente.

>  **Requires: WooCommerce 2.1+**


== Installation ==

 1. Instalar o WooCommerce 2.1 + na sua loja, se já tem instalado pode
    ignorar esse passo.

 2. Instalar e ativar o Plugin PIX for WooCommerce, há três maneiras de instalar:

  - Baixar e descompactar o arquivo `*.zip` na pasta  `/wp-content/plugins/` ;
  - Fazer o upload do arquivo `*.zip`  via plugins do WordPress em  **Plugins &gt; Add New &gt; Upload**
  - Instalar e ativar o plugin por meio do menu **Plugins** no WordPress

3. Vá para  **WooCommerce &gt; Settings &gt; Payments** e selecione "Pix" para configurar.
4. Na página de Pagamentos adicione sua chave PIX e um número  WhatsApp para receber os comprovantes dos seus clientes.

== Contribute ==



You can contribute with the code on [GitHub](https://github.com/InCuca/woocommerce-pix).



== Credits ==



*  [Claudio Sanches](https://claudiosanches.com/) - we base this plugin on one of his plugins



== Changelog ==



= 2020.12.14 - version 1.0.0 =

* Initial Release

= 2021.01.21 - version 1.1.0 =

* Add Telegram and E-mail as sharing methods

= 2021.01.21 - version 1.1.1 =

* Add required fields for compatibility with more banks

= 2021.01.21 - version 1.1.2 =

* Remove stylesheet loading

= 2021.01.27 - version 1.2.0 =

* New QR Code generator to lower PHP requirements