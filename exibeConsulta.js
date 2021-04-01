function exibeConsultaVeiculo(json) {
	var i = 0;
	for(var key in json) {
		var cls = ++i%2 == 0 ? "parentOdd" : "parentEven"; 
		var tr = "<tr class='" + cls + "' id='tr" + json[key]["id"] + "'>"
				+	"<td>" + json[key]["id"] + "</td>"
				+	"<td>" + json[key]["marca"] + "</td>"
				+	"<td>" + json[key]["modelo"] + "</td>"
				+	"<td>" + json[key]["tipo"] + "</td>"
				+	"<td>" + json[key]["placa"] + "</td>"
				+	"<td class='textCenter' width='60px' style='padding-left:10px; padding-right:10px; vertical-align:middle;'>"
				+		"<img src='./img/edit.png' alt='PUT' height='15' width='15' style='float:left; cursor:pointer;'></a>"
                +       "<img src='./img/trash.png' alt='DELETE' height='15' width='15' style='float:right; cursor:pointer;'></a></td>"
				+ "</tr>";
		$("#tableResults tbody").append(tr);
	}
}


function exibeConsultaTipoVeiculo(json) {
	var i = 0;
	for(var key in json) {
		var cls = ++i%2 === 0 ? "parentEven" : "parentOdd"; 
		var tr = "<tr class='" + cls + "' id='tr" + json[key]["id"] + "'>"
				+	"<td>" + json[key]["id"] + "</td>"
				+	"<td>" + json[key]["tipo"] + "</td>"
				+	"<td>" + json[key]["descricao"] + "</td>"
				+	"<td class='textCenter' width='60px' style='padding-left:10px; padding-right:10px; vertical-align:middle;'>"
				+		"<img src='./img/edit.png' alt='PUT' height='15' width='15' style='float:left; cursor:pointer;'></a>"
                +       "<img src='./img/trash.png' alt='DELETE' height='15' width='15' style='float:right; cursor:pointer;'></a></td>"
				+ "</tr>";
		$("#tableResults tbody").append(tr);
	}
}