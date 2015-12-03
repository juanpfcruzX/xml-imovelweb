<?php
// XML PARA EXPORTAÇÃO DE IMOVEIS PARA O PORTAL IMOVELWEB
include "conexao.php";
//$endereco = "http://".$_SERVER['SERVER_NAME']."/";



function removerCaracter($str){
  $remover = array("à" => "a","á" => "a","ã" => "a","â" => "a","é" => "e","ê" => "e","ì" => 
                   "i","í" => "i","ó" => "o","õ" => "o","ô" => "o","ú" => "u","ü" => "u","ç" => 
                   "c","À" => "A","Á" => "A","Ã" => "A","Â" => "A","É" => "E","Ê" => "E","Í" => 
                   "I","Ó" => "O","Õ" => "O","Ô" => "O","Ù" => "U","Ú" => "U","Ü" => "U"," " => "-");
  return str_replace("£", "", str_replace(",", "", str_replace(".", "",strtolower(strtr($str, $remover)))));
 }

$sql = mysql_query("SELECT i.*, date_format(i.datacadastro, '%d/%m/%Y') AS datacadastro, date_format(i.dataatualizacao, '%d/%m/%Y') AS dataatualizacao, 
	                t.tipo AS tipo, c.nome AS cidade, f.fase AS fase, b.nome AS bairro, p.imagem AS imgprincipal, u.nome AS corretor, if(i.negociacao_id=1, 'Vender', 'Alugar') AS negociacao
                    FROM tb_imoveis AS i INNER JOIN tb_tipos AS t
                    ON t.id = i.tipo_id
                    INNER JOIN tb_fases AS f
                    ON f.id = i.fase_id
                    INNER JOIN tb_cidade AS c
                    ON c.id = i.cidade_id
                    INNER JOIN tb_bairro AS b
                    ON b.id = i.bairro_id
                    INNER JOIN tb_imagens AS p
                    ON p.id = i.imagem
                    INNER JOIN tb_usuarios AS u
                    ON u.id = i.user01_id 
                    WHERE vendido = 0 ORDER BY id") or die(mysql_error());
		
//Abrindo documento xml para integração com o Portal IMOVELWEB
$xml = "<?xml version="1.0" encoding="iso-8859-1"?>";
 
//Abre bloco do xml
$xml .= "<Carga xmlns:xsi=http://www.w3.org/2001/XMLSchema-instancexmlns:xsd='http://www.w3.org/2001/XMLSchema'>";

		$xml .= "<Configuracao>";
			$xml .= "<RetornoViaWebservice>1</RetornoViaWebservice>";
		$xml .= "</Configuracao>";
		
		$xml .= "<Imoveis>";

	while ($row = mysql_fetch_array($sql)){
			
			$string = $row['tipo']."-com-".$row['quarto_id']."-quartos-no-bairro-".$row['bairro']."-em-".$row['cidade']."-com-".$row['areaconstruida']."m2";
			$string = removerCaracter($string);

			$xml .= "<Imovel>";
				$xml .= "<CodigoCentralVendas>12345</CodigoCentralVendas>";
				$xml .= "<CodigoImovel>$row[id]</CodigoImovel>";
				$xml .= "<TipoImovel>";
						if($row['tipo'] == 'Apartamento' ){
						$xml .= "<TipoImovel>Residential / Apartment</TipoImovel>";
						}
						if($row['tipo'] == 'Casa' ){
							$xml .= "<TipoImovel>Residential / Home</TipoImovel>";
						}
						if($row['tipo'] == 'Terreno' ){
							$xml .= "<TipoImovel>Commercial / Building</TipoImovel>";
						}
						if($row['tipo'] == 'Cobertura' ){
							$xml .= "<TipoImovel>Residential / Apartment</TipoImovel>";
						}
						if($row['tipo'] == 'Comercial' ){
							$xml .= "<TipoImovel>Commercial / Business</TipoImovel>";
						}
						if($row['tipo'] == 'Flat' ){
							$xml .= "<TipoImovel>Residential / Flat</TipoImovel>";
						}
						if($row['tipo'] == 'Galpão' ){
							$xml .= "<TipoImovel>Residential / Hangar</TipoImovel>";
						}
						if($row['tipo'] == 'Granja' ){
							$xml .= "<TipoImovel>Residential / Farm Ranch</TipoImovel>";
						}
				$xml .= "</TipoImovel>";
			$xml .= "</Imovel>";
		
	}//fecha while 

//fechando bloco do xml
		$xml .= "</Imoveis>";
		
$xml .= "</ListingDataFeed>";

	file_put_contents('../../xml/iw_ofertas.xml',$xml);	

?>

