<style>
    #wpadminbar {
        display: none;
    }

    #wpbody {
        padding: 0;
    }

    #wpcontent {
        padding: 0 !important;
    }

    #screen-meta-links{
        display: none;
    }

    .preview-box {
        position: fixed;
        width: 100%;
        height: 100%;
        background: url(<?php echo WPTN_URL . 'assets/images/wptn-chatbox-bgimg.jpg'; ?>) no-repeat;
        background-position: center center;
        white-space: pre-line;
    }

    .preview-box {
        background-size: cover;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    .preview-box p {
        margin: 0;
    }

    .preview-box .message {
        max-width: 80%;
        font-family: 'Hind Madurai', sans-serif;
        background: #ffffff;
        padding: 4px 10px 2px 10px;
        margin: 15px auto 15px 10px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        box-shadow: 0 1px 3px -2px #bebebe;
    }

    .preview-box .message .name {
        font-family: 'Rubik', sans-serif;
        font-weight: 300;
        margin: 0 0 6px 0;
        font-size: 14px;
        color: #838383;
        line-height: 0px;
        margin: 0;
    }

    .preview-box .message .message-text {
        margin-bottom: 8px;
        word-wrap: break-word;
        font-size: 14px !important;
        line-height: normal;
        margin: 0;
    }

    .preview-box .message .date {
        font-size: 9px;
        font-family: 'Rubik', sans-serif;
        color: #838383;
        line-height: 0px;
        margin: 0;
    }
</style>
<div class="preview-box">
    <div class="message">
        <h6 class="name"><?php echo $msg_row->channel_name; ?></h6>
        <p class="message-text"><?php echo $msg_row->message; ?></p>
        <span class="date"><?php echo $msg_row->date; ?></span>
    </div>
</div>
