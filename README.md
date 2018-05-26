Hlibs para Adianti - Rodrigo Moglia (moglia@interatia.com) - Fernando Pasqueto - Alexandre InDevWeb
Cansados de experar por atualizações estamos fazendo algumas atualizações para comportar necessidades
que surgiram ao longo do desenvolvimento de alguns projetos.

* Hlibs para Adianti
* @copyright  Copyright (c) 2018 Interatia Sistemas de Informação. (http://www.interatia.com)
* @license    http://www.adianti.com.br/framework-license

Para que a classe funcione deve-se jogar o diretório em app\lib\hlib.
Caso queira usar os métodos Back de HCoreApplication e da HWindow

é necessário modificar o index.php e engine.php na raiz do framework.

index.php  //Adicionar esta linha no código próximo a linha 23

```
    HCoreApplication::setHistory($_REQUEST);


```
engine.php //Adcionar esta linha no codigo proximo a linha 14

```
    HCoreApplication::setHistory($_REQUEST); 
```

HWindow

//Determina uma ação a ser executada ao clicar no botão fechar
 ```
  parent::closeAction($class, $method = NULL, $parameters = NULL, $callback = NULL, $type='load')

  ```

//Volta ato 4 níveis apartir do ultimo controler aberto
```
closeBack($level=1, $type='load', $extra_param=array())
```

### HCoreApplication

//Retorna um objeto tipo TActionLink com o nível a retornar desejado
```
HCoreApplication::linkBack($value, $level=1, $extra_param=array(), $color = null, $size = null, $decoration = null, $icon = null)
```

//Retorna um objeto tipo TAction com o nível a retornar desejado pode ser usado nos botoes como action normal
```
HCoreApplication::actionBack($level=1,$extra_param=array())
```
//Redireciona para uma determinado controller presente no historico de acordo com o elemento de nível informado ato 4 niveis
```
HCoreApplication::gotoBack($level=1,$extra_param=array()) 
```
//Carrega para uma determinado controller presente no histórico de acordo com o elemento de nível informado ato 4 níveis
```
HCoreApplication::loadBack($level=1,$extra_param=array()) 
```

### HRepository - funções de agregação adicionais para TRepository

```

TTransaction::open(conexao.ini);
$repository = new HRepository('Model');
$retorno = $repository->count('coluna');
print_r($retorno);
echo "<br/>";
$retorno = $repository->sum('coluna');
print_r($retorno);
echo "<br/>";
$retorno = $repository->max('coluna');
print_r($retorno);
echo "<br/>";
$retorno = $repository->min('coluna');
print_r($retorno);
echo "<br/>";
$retorno = $repository->avg('coluna');
print_r($retorno);
echo "<br/>";
TTransaction::close();

```

### HPanelGroup
```

HPanelGroup::($title = NULL, $background = NULL, $color = NULL) //parametros de cor de fundo e fonte

```
#### Funcoes de formatação comuns (menos codigo no grid)

```
$coluna->setTransformer([HDateFormat::class, 'date2br']); ou 
HDateFormat::date2us($valor) // formata data novamente para ser armazenada

$coluna->setTransformer([HDateTimeFormat::class, 'datetime2br']); ou 
HDateTimeFormat::datetime2us($valor) // formata data novamente para ser armazenada

$coluna->setTransformer([HMoneyFormat::class, 'reais']); oi
HMoneyFormat::numeric($valor) // formata o numero novamente para ser armazenado

```

### HDebug Depuracao Simples

```
HDebug::debug($var,'titulo'); //retorna o valor como dialog
HDebug::raw($var,'titulo'); //retorna o valor bruto
HDebug::box($var,'titulo'); //retorna o valor dentro de uma textarea

```