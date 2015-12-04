<?php
// XML PARA EXPORTAÇÃO DE IMOVEIS PARA O PORTAL IMOVELWEB

/* CHAMANDA PARA A CONEXÃO COM O BANCO DE DADOS */
include "conexao.php";

/* GERAR URL DO SERVIDOR */
$endereco = "http://".$_SERVER['SERVER_NAME']."/";

/* ===================================
REMOVE TODOS OS CARACTERES ESPECIAIS 
PARA NÃO DAR CONFLITO COM AS TAGS E 
PARA AS URLs AMIGÁVEIS
====================================*/
function removerCaracter($str){
  $remover = array("à" => "a","á" => "a","ã" => "a","â" => "a","é" => "e","ê" => "e","ì" => 
                   "i","í" => "i","ó" => "o","õ" => "o","ô" => "o","ú" => "u","ü" => "u","ç" => 
                   "c","À" => "A","Á" => "A","Ã" => "A","Â" => "A","É" => "E","Ê" => "E","Í" => 
                   "I","Ó" => "O","Õ" => "O","Ô" => "O","Ù" => "U","Ú" => "U","Ü" => "U"," " => "-");
  return str_replace("£", "", str_replace(",", "", str_replace(".", "",strtolower(strtr($str, $remover)))));
 }

/* =========================================
SELECT EM TODAS AS TABELAS NO BANCO DE DADOS 
===========================================*/
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
		
//ABRINDO O ARQUIVO XML INSERIDO NA VARIAVEL $xml
$xml = "<?xml version='1.0' encoding='iso-8859-1'?>";
 
//BLOCO COM AS TAGS PARA MONTAR A ESTRUTURA XML
$xml .= "<Carga xmlns:xsi=http://www.w3.org/2001/XMLSchema-instancexmlns:xsd='http://www.w3.org/2001/XMLSchema'>";

		$xml .= "<Configuracao>";
			$xml .= "<RetornoViaWebservice>1</RetornoViaWebservice>";
		$xml .= "</Configuracao>";
		
		$xml .= "<Imoveis>";
	
	/* LOOP EM TODOS OS DADOS DO BANCO */
	while ($row = mysql_fetch_array($sql)){

			$xml .= "<Imovel>";

				$xml .= "<CodigoCentralVendas>793252</CodigoCentralVendas>";
				$xml .= "<CodigoImovel>".$row['id']."</CodigoImovel>";
				$xml .= "<TipoImovel>".$row['tipo']."</TipoImovel>";
				$xml .= "<UF>".$row['uf']."</UF>";
				$xml .= "<Cidade>".$row['cidade']."</Cidade>";
				$xml .= "<Bairro>".$row['bairro']."</Bairro>";
				$xml .= "<Endereco>".$row['endereco']."</Endereco>";
				$xml .= "<AreaTotal>".$row['areaconstruida']."</AreaTotal>";
				$xml .= "<UnidadeMetrica>m²</UnidadeMetrica>";
				$xml .= "<QtdDormitorios>".$row['quarto_id']."</QtdDormitorios>";
				$xml .= "<QtdSuites>".$row['suite_id']."</QtdSuites>";
				$xml .= "<QtdVagas>".$row['vaga_id']."</QtdVagas>";
				$xml .= "<Observacoes><![CDATA[".utf8_decode(strip_tags(str_replace('&nbsp;', ' ', $row['descricao'])))."]]></Observacoes>";
				$xml .= "<Foto>";
					$query = mysql_query("SELECT * FROM tb_imagens WHERE imovel_id = $row[id] AND id <> $row[imagem] ORDER BY ordem LIMIT 1") or die(mysql_error());	
					while($linha = mysql_fetch_array($query)){	
						$xml .= "<URLArquivo>http://www.cinataimoveis.com.br/admin/includes/upload/".$linha['imagem']."</URLArquivo>";
				    }
					$xml .= "<Principal>1</Principal>";
				$xml .= "</Foto>";

			$xml .= "</Imovel>";
		
	}//FIM DO LOOP 

//FIM DO BLOCO XML
		$xml .= "</Imoveis>";
		
$xml .= "</ListingDataFeed>";
	
	// ARQUIVO DE DESTINO COM EXTENSÃO .xml
	file_put_contents('../../xml/iw_ofertas.xml',$xml);	

?>

