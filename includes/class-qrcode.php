<?php

/**
 * QRCode Generator Class based on BR Code Spec
 *
 * @see https://www.bcb.gov.br/content/estabilidadefinanceira/spb_docs/ManualBRCode.pdf
 * @see https://www.bcb.gov.br/content/estabilidadefinanceira/forumpireunioes/AnexoI-PadroesParaIniciacaodoPix.pdf
 *
 * @package Pix_For_WooCommerce
 * @version 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ICPFW_PAYLOAD_FORMAT_INDICATOR', 00);
define('ICPFW_POINT_OF_INITIATION_METHOD', 01);
define('ICPFW_MERCHANT_ACCOUNT_INFORMATION', 26);
define('ICPFW_MERCHANT_CATEGORY_CODE', 52);
define('ICPFW_TRANSACTION_CURRENCY', 53);
define('ICPFW_TRANSACTION_AMOUNT', 54);
define('ICPFW_COUNTRY_CODE', 58);
define('ICPFW_MERCHANT_NAME', 59);
define('ICPFW_MERCHANT_CITY', 60);
define('ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE', 62);
define('ICPFW_CRC16', 63);
define('ICPFW_GUI', 0);
define('ICPFW_CHAVE', 1);
define('ICPFW_INFO', 2);
define('ICPFW_TXID', 5);

define('ICPFW_METHOD_ONCE', 12);

class ICPFW_QRCode
{
    /**
     * Constructor function to initialize default values
     */
    public function __construct()
    {
        $ICPFW_MERCHANT_ACCOUNT_INFORMATION = new ICPFW_EMV();
        $ICPFW_MERCHANT_ACCOUNT_INFORMATION->set(ICPFW_GUI, 'br.gov.bcb.pix');
        $ICPFW_MERCHANT_ACCOUNT_INFORMATION->set(ICPFW_CHAVE, '');

        $ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE = new ICPFW_EMV();
        $ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE->set(ICPFW_TXID, '***');

        $this->emv = new ICPFW_EMV();
        $this->emv->set(ICPFW_PAYLOAD_FORMAT_INDICATOR, '01');
        $this->emv->set(ICPFW_POINT_OF_INITIATION_METHOD, '12');
        $this->emv->set(ICPFW_MERCHANT_ACCOUNT_INFORMATION, $ICPFW_MERCHANT_ACCOUNT_INFORMATION);
        $this->emv->set(ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE, $ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE);
        $this->emv->set(ICPFW_MERCHANT_CATEGORY_CODE, '0000');
        $this->emv->set(ICPFW_TRANSACTION_CURRENCY, '986');
        $this->emv->set(ICPFW_COUNTRY_CODE, 'BR');
        $this->emv->set(ICPFW_CRC16, 'FFFF');
    }

    /**
     * Updates the merchant key
     *
     * @param string $chave Chave PIX (CPF, CNPJ, E-Mail, Telefone ou EVP)
     * @return QRCode This object for chaining
     */
    public function chave($chave)
    {
        $ICPFW_MERCHANT_ACCOUNT_INFORMATION = $this->emv->get(ICPFW_MERCHANT_ACCOUNT_INFORMATION);
        $ICPFW_MERCHANT_ACCOUNT_INFORMATION->set(ICPFW_CHAVE, $chave);
        $this->emv->set(ICPFW_MERCHANT_ACCOUNT_INFORMATION, $ICPFW_MERCHANT_ACCOUNT_INFORMATION);
        return $this;
    }

    /**
     * Updates the transaction currency
     *
     * @see https://pt.wikipedia.org/wiki/ISO_4217
     *
     * @param integer $moeda ISO 4217 Currency Code
     * @return QRCode This object for chaining
     */
    public function moeda($moeda)
    {
        $this->emv->set(ICPFW_TRANSACTION_CURRENCY, str_pad($moeda, 3, '0', STR_PAD_LEFT));
        return $this;
    }

    /**
     * Updates the merchant name
     *
     * @param string $lojista
     * @return QRCode This object for chaining
     */
    public function lojista($lojista)
    {
        $this->emv->set(ICPFW_MERCHANT_NAME, $lojista);
        return $this;
    }

    /**
     * Updates the merchant city
     *
     * @param string $cidade
     * @return QRCode This object for chaining
     */
    public function cidade($cidade)
    {
        $this->emv->set(ICPFW_MERCHANT_CITY, $cidade);
        return $this;
    }

    /**
     * Updates the merchant country
     *
     * @see https://www.iban.com/country-codes
     *
     * @param string $pais Alpha-2 Country Code as of ISO 3166
     * @return QRCode This object for chaining
     */
    public function pais($pais)
    {
        $this->emv->set(ICPFW_COUNTRY_CODE, mb_substr($pais, 0, 2));
        return $this;
    }

    /**
     * Updates the transaction amount
     *
     * @param float $valor
     * @return QRCode This object for chaining
     */
    public function valor($valor)
    {
        $valor = number_format($valor, 2, '.', '');
        $this->emv->set(ICPFW_TRANSACTION_AMOUNT, $valor);
        return $this;
    }

    /**
     * Updates the additional information field
     *
     * @param string $info
     * @return QRCode This object for chaining
     */
    public function info($info)
    {
        $ICPFW_MERCHANT_ACCOUNT_INFORMATION = $this->emv->get(ICPFW_MERCHANT_ACCOUNT_INFORMATION);
        $ICPFW_MERCHANT_ACCOUNT_INFORMATION->set(ICPFW_INFO, $info);
        $this->emv->set(ICPFW_MERCHANT_ACCOUNT_INFORMATION, $ICPFW_MERCHANT_ACCOUNT_INFORMATION);
        return $this;
    }

    /**
     * Updates the transaction ID
     *
     * @param string $txId
     * @return QRCode This object for chaining
     */
    public function txId($txId)
    {
        $ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE = $this->emv->get(ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE);
        $ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE->set(ICPFW_TXID, $txId);
        $this->emv->set(ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE, $ICPFW_ADDITIONAL_DATA_FIELD_TEMPLATE);
        return $this;
    }

    /**
     * Updates the merchant category code
     *
     * @param string $codigoCategoria 4-digit string
     * @return QRCode This object for chaining
     */
    public function codigoCategoria($codigoCategoria)
    {
        $this->emv->set(
            ICPFW_MERCHANT_CATEGORY_CODE,
            mb_substr(str_pad($codigoCategoria, 4, '0', STR_PAD_LEFT), 0, 4)
        );
        return $this;
    }

    /**
     * Outputs the DataRecord under EMVCo Specification
     *
     * @see https://www.emvco.com/wp-content/uploads/documents/EMVCo-Merchant-Presented-QR-Specification-v1-1.pdf
     *
     * @return string
     */
    public function toString()
    {
        return $this->emv->__toString();
    }

    /**
     * Outputs the Payment Link
     *
     * @see https://www.bcb.gov.br/content/estabilidadefinanceira/forumpireunioes/AnexoI-PadroesParaIniciacaodoPix.pdf
     *
     * @return string
     */
    public function toLink()
    {
        return 'https://pix.bcb.gov.br/qr/' . base64_encode($this->toString());
    }

    /**
     * Outputs the Payment Link
     *
     * @see https://www.bcb.gov.br/content/estabilidadefinanceira/forumpireunioes/AnexoI-PadroesParaIniciacaodoPix.pdf
     *
     * @return string
     */
    public function toCode()
    {
        return $this->toString();
    }

    /**
     * Renders and saves the QRCode image to a file
     *
     * @param string $filename The output filename
     * @return QRCode This object for chaining
     */
    public function toImage()
    {
        include dirname(__FILE__) . '/../vendor/php-qrcode/qrcode.php';

        $generator = new ICPFW_Generate_QRCode($this->toString(), $options);

        /* Create bitmap image. */
        $image = $generator->render_image();
        ob_start();
        imagejpeg($image);
        $contents =  ob_get_contents();
        ob_end_clean();
        imagedestroy($image);

        $img_data = "data:image/jpg;base64," . base64_encode($contents);

        return $img_data;
    }
}
