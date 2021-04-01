//função que recebe os parâmetros e dados e envia a api 
function salvaDados(map, action, dataObj) {

    var dataString = "";
    msg = "Apagado com sucesso";

    //se a ação for inserir ou alterar 
    if(action === "POST" || action === "PUT"){
        dataString = JSON.stringify(dataObj); //transforma o objeto jSON em texto
        var msg = "Salvo com sucesso";  //muda a mensagem para salvo
    }

    //envia os parâmetros e os dados para a api
    $.ajax({
        type: action,
        url: url + map,
        contentType: "application/json",
        dataType: "json",
        data: dataString,
        success: function(res, status) {
            //se a mensagem retornada for de sucesso, fecha o modal e recarrega a página inicial
            okDialog.show(msg, {onHide: function () {location.reload();}});
        },
        error: function(res, status) {
            //se a mensagem for de erro, exibe o erro na tela e volta para o modal
            errorDialog.show(res.responseJSON.titulo);
        }
    });

}