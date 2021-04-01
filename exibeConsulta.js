//função que recebe o json da api e adiciona os veículos na tabela da página inicial
function exibeConsultaVeiculo(json) {
	var i = 0;
	//percorre o json e extrai as informações
	for(var key in json) {
		var cls = ++i%2 == 0 ? "parentOdd" : "parentEven"; 
		var tr = "<tr class='" + cls + "' id='tr" + json[key]["id"] + "'>"
				+	"<td class='textCenter'>" + json[key]["id"] + "</td>"
				+	"<td class='textCenter'>" + json[key]["marca"] + "</td>"
				+	"<td class='textCenter'>" + json[key]["modelo"] + "</td>"
				+	"<td class='textCenter'>" + json[key]["tipo"] + "</td>"
				+	"<td class='textCenter'>" + json[key]["placa"] + "</td>"
				+	"<td class='textCenter' width='60px' style='padding-left:10px; padding-right:10px; vertical-align:middle;'>"
				+		"<img src='./img/edit.png' alt='PUT' height='15' width='15' style='float:left; cursor:pointer;'></a>"
                +       "<img src='./img/trash.png' alt='DELETE' height='15' width='15' style='float:right; cursor:pointer;'></a></td>"
				+ "</tr>";
		$("#tableResults tbody").append(tr);
	}
}

//função que recebe o json da api e adiciona os tipos de veículos na tabela da página inicial
function exibeConsultaTipoVeiculo(json) {
	var i = 0;
	//percorre o json e extrai as informações
	for(var key in json) {
		var cls = ++i%2 === 0 ? "parentEven" : "parentOdd"; 
		var tr = "<tr class='" + cls + "' id='tr" + json[key]["id"] + "'>"
				+	"<td class='textCenter'>" + json[key]["id"] + "</td>"
				+	"<td class='textCenter'>" + json[key]["tipo"] + "</td>"
				+	"<td>" + json[key]["descricao"] + "</td>"
				+	"<td class='textCenter' width='60px' style='padding-left:10px; padding-right:10px; vertical-align:middle;'>"
				+		"<img src='./img/edit.png' alt='PUT' height='15' width='15' style='float:left; cursor:pointer;'></a>"
                +       "<img src='./img/trash.png' alt='DELETE' height='15' width='15' style='float:right; cursor:pointer;'></a></td>"
				+ "</tr>";
		$("#tableResults tbody").append(tr);
	}
}