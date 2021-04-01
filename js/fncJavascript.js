function formatData(obj, event)
{
	var tecla;
	var key;
	var data = new String(obj.value);
	if(navigator.appName.indexOf('Netscape')!== -1){
            tecla= event.which;
        }
	else{
            tecla= event.keyCode;
        }

	key = String.fromCharCode(tecla);

	if (tecla === 13){
            return false;
        }

	if (tecla === 'BACKSPACE' || tecla === 8 || tecla === 0){
            return true;
        }

	if (data.length <= 10)
	{
		if (isNumber(key)){
			if (data.length === 2)
			obj.value = data.substr(0,2)+'/';

			if (data.length === 5)
			obj.value = data.substr(0,2)+'/'+data.substr(3,2)+'/'+data.substr(6,4);
		}
		else{
                    return false;
                }
	}
	else
	{
		return false;
	}

}

function isNumber(caractere)
{

	var strValidos = '0123456789'

	if (strValidos.indexOf( caractere ) === -1 )
	return false;

	return true;
}

/* Carrega o botão Login do menu de acordo com a autenticação do usuário */
function fncBtnLogin(nome = false){
    if(nome){
        $("#btnLogin").hide();
        $("#btnSair").show();
        $("#lblBemVindo").html("Bem Vindo <span style='color:white; font-size:17px;'>" + nome + "<span>");
    }
    else{
        $("#btnLogin").show();
        $("#btnSair").hide();
        $("#lblBemVindo").html("");
    }
}

/* valida se a data digitada é válida */
function isValidDate(date)
{
    var dia = date.substr(0,2);
    var mes = date.substr(3,2);
    var ano = date.substr(6,4);
    date = mes + '/' + dia + '/' + ano;

    var matches = /^(\d{2})[-\/](\d{2})[-\/](\d{4})$/.exec(date);
    if (matches == null) return false;
    var d = matches[2];
    var m = matches[1] - 1;
    var y = matches[3];
    var composedDate = new Date(y, m, d);
    return composedDate.getDate() == d &&
            composedDate.getMonth() == m &&
            composedDate.getFullYear() == y;
}
