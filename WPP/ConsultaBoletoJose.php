<?php
//header('Access-Control-Allow-Origin: *');
//header('Content-Type: application/json');
class Boleto {
	private $Login;
	private $Senha;
	
	public function __construct($Login="",$Senha=""){
		
		$this->Login = $Login;
		$this->Senha = $Senha;
	}
	
	
	public function doCurl(){
		
		$url = 'http://7397.webmikrotik.com/actions-auth-central.php';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('formulario' => 'central_cliente', 'usuario'=>$this->Login, 'senha'=>$this->Senha));
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd () . '/cookieBoleto.txt' );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36");
		curl_setopt($ch, CURLOPT_REFERER, "http://7397.webmikrotik.com");
		$page = curl_exec($ch) or die(curl_error($ch));
		$status = curl_getinfo($ch);
		
		return $page;
	}
	
	private function matchRegex($strContent, $strRegex, $intIndex = NULL) {
        $arrMatches = FALSE;
        preg_match_all($strRegex, $strContent, $arrMatches);
        if ($arrMatches === FALSE)
            return FALSE;
        if ($intIndex != NULL && is_int($intIndex)) {
            if ($arrMatches[$intIndex]) {
                return $arrMatches[$intIndex][0];
            }
            return FALSE;
        }
        return $arrMatches;
    }
	
	private function getScrape() {

        if (!isset($this->_strSource) || $this->_strSource == null || $this->_strSource == "") {
            $this->_strSource = $this->doCurl();
        }
		
        return $this->_strSource;
		//return preg_replace('/\s+/', '',$this->_strSource);
    }
	
	public function LinkBoleto(){
		$conteudo = preg_replace('/\s+/', '',$this->getScrape());
		
		//print_r($conteudo); exit;
		$tot = $this->matchRegex($conteudo, '~<divclass="row-flexbg-red-leftmb-5">(.*)</div></div></div></div></div></div>~Uis');
		foreach($tot[1] as $dv){
		$id = $this->matchRegex($dv, '~<aclass="btnbtn-grid-default"href="javascript:imprimirSolo[(]\'(.*)\'[)]~Uis',1);
		$Vencimento = $this->matchRegex($dv, '~<iclass="ti-calendar"></i></div><divclass="text-content"><strong>(.*)</strong>~Uis',1);
		$Valor = $this->matchRegex($dv, '~<iclass="ti-calendar"></i></div><divclass="text-content"><strong>(?:.*)</strong><p>(.*)</p>~Uis',1);
		
		//print_r($Valor);
		echo "Olá {{NOME}}, segue seu boleto.\nVencimento : ".$Vencimento."\nValor : ".$Valor."Link Boleto : http://7397.webmikrotik.com/ImpressaoNF_Boleto_Nova.php?id=".$id;
		}
		
		
		//print_r($tot);
	}
	
}

$Face = new Boleto("zilda-carvalho-silva","123");
echo $Face->LinkBoleto();