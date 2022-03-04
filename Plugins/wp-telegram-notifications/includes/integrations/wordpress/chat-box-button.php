<!-- Button -->
<?php
// Check the button is on the left or right side.
if ( \WPTN\Option::getOption( 'wordpress_chat_button_box_position' ) == "left" ): ?>
    <style>
        @media (min-width: 300px) and (max-width: 840px) {

            .wptn-chat-wrapper {
                left: 0 !important;
                right: 0 !important;
            }

            .wptn-chat-wrapper.open {
                right: auto !important;
                left: 0px !important;
            }
            .wptn-button-wrapper {
                left: 20px !important;
            }

            .emoji-picker {
                left: 160px !important;
                bottom: 50px !important;
                right: auto;
            }
        }
        .wptn-button-wrapper {
            right: auto;
            left: 50px;
        }

        .wptn-chat-wrapper {
            right: auto;
            left: 120px;
        }

        .wptn-chat-wrapper.open {
            right: auto;
            left: 120px;
        }

        .emoji-picker {
            left: 160px !important;
            right: auto;
        }

    </style>
<?php endif; ?>
<div class="wptn-button-wrapper">
    <button class="wptn-button-toggle">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 240 240">
            <defs>
                <linearGradient id="b" x1="0.6667" y1="0.1667" x2="0.4167" y2="0.75">
                    <stop stop-color="#37aee2" offset="0"/>
                    <stop stop-color="#1e96c8" offset="1"/>
                </linearGradient>
                <linearGradient id="w" x1="0.6597" y1="0.4369" x2="0.8512" y2="0.8024">
                    <stop stop-color="#eff7fc" offset="0"/>
                    <stop stop-color="#fff" offset="1"/>
                </linearGradient>
            </defs>
            <circle cx="120" cy="120" r="120" fill="url(#b)"/>
            <path fill="#c8daea" d="m98 175c-3.8876 0-3.227-1.4679-4.5678-5.1695L82 132.2059 170 80"/>
            <path fill="#a9c9dd" d="m98 175c3 0 4.3255-1.372 6-3l16-15.558-19.958-12.035"/>
            <path fill="url(#w)" d="m100.04 144.41 48.36 35.729c5.5185 3.0449 9.5014 1.4684 10.876-5.1235l19.685-92.763c2.0154-8.0802-3.0801-11.745-8.3594-9.3482l-115.59 44.571c-7.8901 3.1647-7.8441 7.5666-1.4382 9.528l29.663 9.2583 68.673-43.325c3.2419-1.9659 6.2173-0.90899 3.7752 1.2584"/>
        </svg>
    </button>
</div><!-- Button --><!-- Message Box -->
<div class="wptn-chat-wrapper">
    <div class="wptn-user-info">
        <div class="user-info-form">
            <div class="user-info-form__header">
                <h5>Enter Your Info</h5>
            </div>
            <div class="user-info-form__body">
                <p>Please Enter Your number or ID.</p>
                <form>
                    <input placeholder="Phone" id="user-phone" type="text" name="phone">
                    <input placeholder="@ID" id="user-id" type="text" name="id_user">
                    <input type="submit" value="SUBMIT">
                </form>

                <div class="wp-telegram-logo">
                    <a class="tooltip" href="https://wp-telegram.com"><img src="<?php echo WPTN_URL . 'assets/images/wptn-chatbox-telegram-logo.png'; ?>" alt="wp-telegram"/><span class="tooltiptext">Powered by WP-Telegram</span></a>
                </div>
            </div>
        </div>
    </div>
    <div class="wptn-chat__box">
        <div class="close-box-button">
            <button class="close-button">
                <svg width="32" version="1.1" xmlns="http://www.w3.org/2000/svg" height="22" viewBox="0 0 64 64" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 64 64">
                    <g>
                        <path fill="#1D1D1B" d="M28.941,31.786L0.613,60.114c-0.787,0.787-0.787,2.062,0,2.849c0.393,0.394,0.909,0.59,1.424,0.59   c0.516,0,1.031-0.196,1.424-0.59l28.541-28.541l28.541,28.541c0.394,0.394,0.909,0.59,1.424,0.59c0.515,0,1.031-0.196,1.424-0.59   c0.787-0.787,0.787-2.062,0-2.849L35.064,31.786L63.41,3.438c0.787-0.787,0.787-2.062,0-2.849c-0.787-0.786-2.062-0.786-2.848,0   L32.003,29.15L3.441,0.59c-0.787-0.786-2.061-0.786-2.848,0c-0.787,0.787-0.787,2.062,0,2.849L28.941,31.786z"/>
                    </g>
                </svg>

            </button>
        </div>
        <div class="wptn-chat__header">
            <div class="wptn-chat__header__avatar">
                <img src="<?php $avatar = \WPTN\Option::getOption( 'wordpress_chat_button_box_avatar' );
				if ( $avatar ) {
					echo $avatar;
				} else {
					echo WPTN_URL . 'assets/images/wptn-default-avatar-chatbox.png';
				} ?>"/>
            </div>
            <div class="wptn-chat__header__name">
                <h3><?php echo \WPTN\Option::getOption( 'wordpress_chat_button_box_title' ) ? \WPTN\Option::getOption( 'wordpress_chat_button_box_title' ) : __( 'WP - Telegram', 'wp-telegram-notifications' ); ?></h3>
            </div>
        </div>
        <div data-simplebar class="wptn-chat__body">
            <div class="wptn-chat__body__messages">

            </div>
        </div>
        <div class="wptn-chat__footer">
            <div class="wptn-chat__footer__form">
                <form>
                    <div class="wptn-emoji-area wptn-form-controls" data-emojiarea="" data-type="unicode" data-global-picker="false">
                        <div class="button-submit">
                            <button class="submit-message">
                                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 490.282 490.282" style="enable-background:new 0 0 490.282 490.282;" xml:space="preserve">
                                <g>
                                    <path d="M0.043,245.197c0.6,10.1,7.3,18.6,17,21.5l179.6,54.3l6.6,123.8c0.3,4.9,3.6,9.2,8.3,10.8c1.3,0.5,2.7,0.7,4,0.7
                                        c3.5,0,6.8-1.4,9.2-4.1l63.5-70.3l90,62.3c4,2.8,8.7,4.3,13.6,4.3c11.3,0,21.1-8,23.5-19.2l74.7-380.7c0.9-4.4-0.8-9-4.2-11.8
                                        c-3.5-2.9-8.2-3.6-12.4-1.9l-459,186.8C5.143,225.897-0.557,235.097,0.043,245.197z M226.043,414.097l-4.1-78.1l46,31.8
                                        L226.043,414.097z M391.443,423.597l-163.8-113.4l229.7-222.2L391.443,423.597z M432.143,78.197l-227.1,219.7l-179.4-54.2
                                        L432.143,78.197z"/>
                                </g>
                            </svg>
                            </button>
                        </div>
                        <div class="emoji-button">
                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 368 368" style="enable-background:new 0 0 368 368;" xml:space="preserve">
                            <g>
                                <g>
                                    <g>
                                        <path d="M184,0C82.544,0,0,82.544,0,184s82.544,184,184,184s184-82.544,184-184S285.456,0,184,0z M184,352
                                            c-92.64,0-168-75.36-168-168S91.36,16,184,16s168,75.36,168,168S276.64,352,184,352z"/>
                                        <path d="M144,152c0-13.232-10.768-24-24-24s-24,10.768-24,24s10.768,24,24,24S144,165.232,144,152z M112,152c0-4.408,3.592-8,8-8
                                            s8,3.592,8,8s-3.592,8-8,8S112,156.408,112,152z"/>
                                        <path d="M248,128c-13.232,0-24,10.768-24,24s10.768,24,24,24s24-10.768,24-24S261.232,128,248,128z M248,160
                                            c-4.408,0-8-3.592-8-8s3.592-8,8-8c4.408,0,8,3.592,8,8S252.408,160,248,160z"/>
                                        <path d="M261.336,226.04c-3.296-2.952-8.36-2.664-11.296,0.624C233.352,245.312,209.288,256,184,256
                                            c-25.28,0-49.352-10.688-66.04-29.336c-2.952-3.288-8-3.576-11.296-0.624c-3.296,2.944-3.568,8-0.624,11.296
                                            C125.76,259.368,154.176,272,184,272c29.832,0,58.248-12.64,77.96-34.664C264.904,234.04,264.624,228.984,261.336,226.04z"/>
                                    </g>
                                </g>
                            </g>
                            </svg>
                        </div>
                        <input id="message-input" placeholder="<?php _e( 'Type Your Message...', 'wp-telegram-notifications' ) ?>" type="text" name="message"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- Message Box -->