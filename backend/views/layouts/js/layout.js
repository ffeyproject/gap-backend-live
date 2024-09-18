setInterval(function(){ check(); }, 10000);

function check() {
    $.ajax({
            method: 'GET',
            //beforeSend: function (jqXHR, settings) {},
            url: checkUrl,
            error: function(jqXHR, textStatus, errorThrown ){
                var errorObj;
                try {
                    errorObj = jQuery.parseJSON(jqXHR.responseText);
                    if(typeof errorObj !='object'){
                        errorObj = {name:"Error", message:jqXHR.responseText};
                    }
                } catch (e) {
                    errorObj = {name:"Error", message:jqXHR.responseText};
                }

                console.log(errorObj);
            },
            success: function(data){
                //console.log(data);

                let notifLen = data.notif.length;
                if(notifLen > 0){}

                let msgLen = data.msg.length;
                if(msgLen > 0){}

                $('#TaskUl').empty(); $("#TaskCounnt1").html("0"); $("#TaskCounnt2").html("0");
                let taskLen = data.task.length;
                if(taskLen > 0){
                    $("#TaskCounnt1").html(taskLen); $("#TaskCounnt2").html(taskLen);
                    $.each(data.task, function(index, value) {
                        $("#TaskUl").append(value);
                    });
                }
            }
        }
    );
}