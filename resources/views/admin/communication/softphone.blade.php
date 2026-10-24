@push('styles')
    <style>
        /* Ensure the iframe takes up the available height in the content area */
        .softphone-container {
            height: calc(100vh - 200px);
            /* Adjust this based on your header height */
            min-height: 600px;
            width: 100%;
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .softphone-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
@endpush

<x-admin.layout title="Web phone">
    <x-admin.page-header title="Web phone" description="Make and receive calls directly from your browser." />

    <x-admin.card>
        <div class="softphone-container">
            <!-- 
                The iframe loads the external portal. 
                Note: If the portal blocks iframe embedding via X-Frame-Options, 
                this will appear blank. 
            -->
            <iframe src="https://amarip.net/" class="softphone-iframe" title="Amar IP Webphone"
                allow="microphone; camera; autoplay; clipboard-read; clipboard-write"
                sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-modals">
                <p>Your browser does not support iframes. Please <a href="https://amarip.net/" target="_blank">click
                        here</a> to open the webphone in a new tab.</p>
            </iframe>
        </div>
    </x-admin.card>
</x-admin.layout>