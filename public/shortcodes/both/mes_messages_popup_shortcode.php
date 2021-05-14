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
    }

    #mes-messages-popup .messages-list{
        padding: 20px;
        background: white;
        border-radius: 10px;
        width: 800px;
    }

    #mes-messages-popup .message-item{
        display: grid;
        grid-template-columns: 1fr 3fr 2fr;
        align-items: center;
    }

    #mes-messages-popup .message-item .message{
        font-size: .8em;
        justify-self: center;
    }


</style>
<div id='mes-messages-popup'>

    <div class="messages-list">
        
        <div class="message-item">
            
            <div class="teacher">Arlene McCoy</div>
            <div class="message">A schedule for you has been set</div>
            <div class="schedule">May 5, 2021 9h00 - 10h45</div>

        </div>

        <div class="message-item">
            
            <div class="teacher">Arlene McCoy</div>
            <div class="message">A schedule for you has been set</div>
            <div class="schedule">May 5, 2021 9h00 - 10h45</div>

        </div>

        <div class="message-item">
            
            <div class="teacher">Arlene McCoy</div>
            <div class="message">A schedule for you has been set</div>
            <div class="schedule">May 5, 2021 9h00 - 10h45</div>

        </div>

    </div>

</div>