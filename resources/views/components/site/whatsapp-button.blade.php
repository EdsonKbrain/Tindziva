@if (setting('whatsapp_enabled') && ($number = setting('whatsapp_number')))
    <a href="https://wa.me/{{ $number }}?text={{ rawurlencode(setting('whatsapp_message', 'Olá! Gostaria de saber mais sobre os serviços da Tindziva.')) }}"
       target="_blank" rel="noopener"
       class="fixed right-5 bottom-5 z-40 grid size-14 place-items-center rounded-full bg-[#25D366] text-white shadow-xl shadow-black/20 transition hover:scale-105 focus-visible:outline-[#25D366]"
       aria-label="{{ site_text('whatsapp_button') }}">
        <svg viewBox="0 0 24 24" class="size-7" fill="currentColor" aria-hidden="true"><path d="M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.79-1.47-1.76-1.65-2.05-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.06 2.88 1.21 3.07c.15.2 2.1 3.2 5.08 4.48.71.31 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35M12.04 21.5h-.01a9.43 9.43 0 0 1-4.8-1.32l-.35-.2-3.57.93.96-3.48-.23-.36a9.4 9.4 0 0 1-1.44-5.02c0-5.2 4.24-9.44 9.45-9.44a9.4 9.4 0 0 1 9.44 9.45c0 5.2-4.24 9.44-9.45 9.44m8.04-17.48A11.3 11.3 0 0 0 12.04.67C5.78.67.67 5.77.67 12.04c0 2 .52 3.96 1.52 5.68L.57 23.33l5.74-1.5a11.4 11.4 0 0 0 5.73 1.46c6.26 0 11.37-5.1 11.37-11.37 0-3.03-1.18-5.89-3.33-8.03"/></svg>
    </a>
@endif
