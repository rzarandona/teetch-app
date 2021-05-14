<style>

    #mes-messages-popup{
        height: 100vh;
        width:100vw;
        position: fixed;
        top:0;
        left: 0;
        z-index: 999;
        background: rgba(0,0,0,.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: "Nunito";
        transition: .1s;
        transform: scale(0);
    }

    #mes-messages-popup .messages-list-container{
        position:relative
    }
    #mes-messages-popup .messages-list{
        padding: 20px;
        background: white;
        border-radius: 10px;
        width: 800px;
        height: 500px;
        overflow-y: scroll;
    }

    #mes-messages-popup .message-item{
        display: grid;
        grid-template-columns: 1fr 3fr 2fr;
        align-items: center;
        padding:20px 10px;
        border-radius:5px;
        margin:10px 0;
    }

    #mes-messages-popup .message-item.unopened{
        background:#3ab890 !important;
        color:white !important;
    }
    #mes-messages-popup .message-item.unopened a{
        color:white !important;
    }


    #mes-messages-popup .message-item:nth-child(odd){
        background:#efefef;
    }

    #mes-messages-popup .message-item .message{
        font-size: .8em;
        justify-self: center;
    }
    #mes-messages-popup .message-item .schedule{
        justify-self: end;
    }

    #mes-messages-popup .mes-messages-close{
        display: block;
        margin-left: auto;
        background: white;
        color: #3ab890;
        padding: 10px 15px;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    #mes-messages-popup .messages-loader{
        position: absolute;
        height: 500px;
        background: rgba(0,0,0,.3);
        width: 100%;
        display: flex;
        align-items: center;
        bottom: 0;
        justify-content: center;
        border-radius: 10px;
        transform: scale(0);
        transition: .2s;
    }

</style>
<div id='mes-messages-popup'>
    <div class="messages-list-container">

        <button class='mes-messages-close'><i class="fas fa-times"></i></button>
        <div class="messages-loader">
            <div class="loader"></div>
        </div>
        <div class="messages-list"></div>
    </div>

</div>

<script>

    function mapMessages(messages){
        console.log(messages);
        jQuery('.messages-list').empty();
        messages.forEach((message)=>{
            jQuery('.messages-list').append(
                `
                    <div class="message-item ${message.status}">
                            
                        <div class="teacher">${message.teacher_name}</div>
                        <div class="message">A schedule for you has been set. <u><a href='${message.zoom_link}' target='_blank'>Join now!</a></u></div>
                        <div class="schedule">May 5, 2021 ${message.timeslot}h00 - ${message.timeslot}h45</div>

                    </div>
                `
        )   
        })
        
        console.log(messages);
    }

    jQuery(document).ready(function($){

        let args = {
            action: 'get_user_messages',
            nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>',
        }

        $.ajax({
            type:'post',
            dataTye:'JSON',
            url:teetchAjax.ajaxurl,
            data: args,
            beforeSend: function(){
                $('.messages-loader').css({
                    'transform': 'scale(1)'
                });
            },
            success: function(response){
                response = response.slice(0, -1);
                response = JSON.parse(response);
                console.log(response.length);
            },
            complete:function(response){
                $('.messages-loader').css({
                    'transform': 'scale(0)'
                });
            }
        
        });


        $('#mes-messages-trigger').click(function(){
            $('#mes-messages-popup').css({
                'transform': 'scale(1)'
            });

            let args = {
                action: 'get_user_messages',
                nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>',
            }

            $.ajax({
                type:'post',
                dataTye:'JSON',
                url:teetchAjax.ajaxurl,
                data: args,
                beforeSend: function(){
                    $('.messages-loader').css({
                        'transform': 'scale(1)'
                    });
                },
                success: function(response){
                    response = response.slice(0, -1);
                    response = JSON.parse(response);
                    mapMessages(response)
                },
                complete:function(response){
                    $('.messages-loader').css({
                        'transform': 'scale(0)'
                    });
                }
            
            });

        })

        $('.mes-messages-close').click(function(){
            $('#mes-messages-popup').css({
                'transform': 'scale(0)'
            });
        })
    })
    

</script>