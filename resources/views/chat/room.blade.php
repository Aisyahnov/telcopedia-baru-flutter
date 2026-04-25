@extends('layouts.app')
@section('title', 'Chat - Telcopedia')

@push('styles')
<style>
    .chat-container { height: calc(100vh - 120px); background: #f0f2f5; overflow: hidden; border-radius: 15px; }
    .chat-sidebar { background: white; border-right: 1px solid #e1e4e8; height: 100%; display: flex; flex-direction: column; }
    .chat-list { overflow-y: auto; flex: 1; }
    .chat-item { padding: 15px; border-bottom: 1px solid #f8f9fa; transition: 0.2s; cursor: pointer; text-decoration: none; color: inherit; display: block; }
    .chat-item:hover { background: #f8f9fa; }
    .chat-item.active { background: #fee2e2; border-left: 4px solid #9F1521; }
    
    .chat-main { height: 100%; display: flex; flex-direction: column; background: #fff; }
    .chat-header { padding: 15px 25px; border-bottom: 1px solid #eee; background: white; z-index: 10; }
    .chat-messages { flex: 1; overflow-y: auto; padding: 25px; background: #fdfdfd; display: flex; flex-direction: column; }
    
    /* Bubble Logic */
    .bubble-wrapper { display: flex; flex-direction: column; margin-bottom: 20px; width: 100%; }
    .is-me { align-items: flex-end; }
    .is-other { align-items: flex-start; }

    .bubble { 
        max-width: 70%; padding: 12px 18px; position: relative; font-size: 0.95rem; 
        line-height: 1.5; box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
    }
    
    /* Me Bubble (Maroon) */
    .is-me .bubble { 
        background: #9F1521; color: white; border-radius: 20px 20px 4px 20px; 
    }
    
    /* Other Bubble (Light Grey) */
    .is-other .bubble { 
        background: #f1f1f1; color: #333; border-radius: 20px 20px 20px 4px; 
    }

    .bubble-time { font-size: 0.7rem; opacity: 0.7; margin-top: 5px; display: block; }
    .is-me .bubble-time { text-align: right; color: rgba(255,255,255,0.7); }

    /* Action Buttons (Floating below) */
    .bubble-actions { display: none; margin-top: 6px; gap: 12px; font-size: 0.75rem; font-weight: 600; }
    .is-me .bubble-actions { justify-content: flex-end; color: #9F1521; }
    .bubble-wrapper:hover .bubble-actions { display: flex; }
    .action-link { cursor: pointer; text-decoration: none; opacity: 0.6; transition: 0.2s; }
    .action-link:hover { opacity: 1; }
    .action-delete:hover { color: #dc3545; }

    .chat-input-area { padding: 20px 25px; border-top: 1px solid #eee; background: white; }
    .chat-input-wrapper { background: #f0f2f5; border-radius: 30px; padding: 5px 15px; display: flex; align-items: center; border: 1px solid transparent; transition: 0.3s; }
    .chat-input-wrapper.edit-mode { border-color: #ffc107; background: #fffdf5; box-shadow: 0 0 15px rgba(255, 193, 7, 0.1); }
    .chat-input { border: none; background: transparent; padding: 10px; width: 100%; font-size: 0.95rem; }
    .chat-input:focus { outline: none; }
    
    .btn-send { width: 42px; height: 42px; border-radius: 50%; background: #9F1521; color: white; display: flex; align-items: center; justify-content: center; border: none; transition: 0.3s; }
    .btn-send:hover { background: #7c111b; transform: scale(1.05); }
    .btn-cancel-edit { color: #dc3545; font-size: 0.8rem; cursor: pointer; margin-right: 15px; font-weight: 600; }

    .product-badge { 
        background: #fff; border: 1px solid #eee; padding: 12px; border-radius: 15px; 
        margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; align-items: center;
    }
    .badge-img { width: 45px; height: 45px; background: #fff5f5; color: #9F1521; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

    /* Animations */
    @keyframes fadeOutDown {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(10px); }
    }
    .msg-removing { animation: fadeOutDown 0.4s ease forwards; pointer-events: none; }
</style>
@endpush

@if(Auth::user()->role !== 'buyer')
    @section('hero_title', 'Ruang Percakapan')
    @section('hero_subtitle', 'Pastikan memberikan respon ramah untuk meningkatkan kepercayaan pembeli.')
    @section('hero_emoji', '')
@endif

@section('content')
    <div class="{{ Auth::user()->role !== 'buyer' ? '' : 'container py-5' }}">
        <div class="row g-0 chat-container border shadow-sm">
            <!-- SIDEBAR: LIST CHATS -->
            <div class="col-md-3 chat-sidebar d-none d-md-flex">
                <div class="p-4 border-bottom bg-light d-flex align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">PESAN SAYA</h6>
                </div>
                <div class="chat-list">
                    @forelse($chats as $c)
                        @php
                            $opponent = ($c->user1_id == auth()->id()) ? $c->user2 : $c->user1;
                            $lastMsg = $c->messages->last();
                        @endphp
                        <a href="{{ route('chat.room', $c->id) }}" class="chat-item {{ isset($chat) && $chat->id == $c->id ? 'active' : '' }} border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="bg-maroon-soft text-maroon rounded-circle d-flex align-items-center justify-content-center me-3 border shadow-sm" style="width: 42px; height: 42px;">
                                    <i class="fa fa-user opacity-50"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="mb-0 fw-bold text-truncate text-dark" style="font-size: 0.85rem;">{{ $opponent->name ?? 'User' }}</h6>
                                    <p class="mb-0 x-small text-muted text-truncate" style="font-size: 0.7rem;">{{ $lastMsg ? $lastMsg->message : 'Mulai chat...' }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center p-5 text-muted x-small fw-bold">KOTAK MASUK KOSONG</div>
                    @endforelse
                </div>
            </div>

            <!-- MAIN CHAT AREA -->
            <div class="col-md-9 chat-main">
                @if(isset($chat))
                    @php $opponent = ($chat->user1_id == auth()->id()) ? $chat->user2 : $chat->user1; @endphp
                    
                    <div class="chat-header d-flex align-items-center justify-content-between p-4 bg-white border-bottom shadow-sm">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('chat.index') }}" class="d-md-none me-3 text-dark"><i class="fa fa-arrow-left"></i></a>
                            <div class="bg-maroon-soft text-maroon rounded-circle d-flex align-items-center justify-content-center me-3 border" style="width: 48px; height: 48px;">
                                <i class="fa fa-user fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $opponent->name ?? 'User' }}</h6>
                                <span class="text-success x-small fw-bold d-block"><i class="fa fa-circle me-1" style="font-size: 6px;"></i> ONLINE</span>
                            </div>
                        </div>
                    </div>

                    <div class="chat-messages" id="message-container">
                        <!-- INFO PRODUK -->
                        <div class="product-badge">
                            <div class="badge-img me-3 border">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="small fw-bold mb-0 text-truncate">{{ $chat->product->name ?? 'Produk Telcopedia' }}</h6>
                                <p class="mb-0 x-small text-danger fw-bold" style="font-size: 0.75rem;">Rp {{ number_format($chat->product->price ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('product.show', $chat->product_id) }}" class="btn btn-sm btn-dark rounded-pill px-3 ms-2" style="font-size: 0.7rem;">Kunjungi</a>
                        </div>

                        @foreach($messages as $msg)
                            @php $isMe = ($msg->sender_id == auth()->id()); @endphp
                            <div class="bubble-wrapper {{ $isMe ? 'is-me' : 'is-other' }}" id="msg-wrapper-{{ $msg->id }}">
                                <div class="bubble">
                                    <span id="msg-text-{{ $msg->id }}">{{ $msg->message }}</span>
                                    <small class="bubble-time">
                                        {{ $msg->created_at->format('H:i') }}
                                    </small>
                                </div>
                                @if($isMe)
                                    <div class="bubble-actions">
                                        <span class="action-link" onclick="enterEditMode({{ $msg->id }}, '{{ addslashes($msg->message) }}')" title="Ubah"><i class="fa fa-pen"></i></span>
                                        <span class="action-link action-delete" onclick="deleteMessage({{ $msg->id }})" title="Hapus"><i class="fa fa-trash"></i></span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="chat-input-area">
                        <div id="edit-indicator" class="small text-warning mb-2 d-none">
                            <i class="fa fa-pen-to-square me-1"></i> Mode Edit: <span class="btn-cancel-edit" onclick="exitEditMode()">Batalkan</span>
                        </div>
                        <form id="chat-form" onsubmit="handleFormSubmit(event)">
                            @csrf
                            <div class="chat-input-wrapper" id="input-wrapper">
                                <input type="text" id="chat-input" placeholder="Tulis pesan..." class="chat-input" autocomplete="off">
                                <button type="submit" class="btn-send shadow-sm" id="btn-submit">
                                    <i class="fa fa-paper-plane" id="btn-icon"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 flex-column text-muted">
                        <div class="p-5 bg-light rounded-circle mb-4 border">
                            <i class="fa-regular fa-comment-dots fa-5x opacity-25"></i>
                        </div>
                        <h5 class="fw-bold">Mulai Percakapan Baru 🔴</h5>
                        <p class="small text-center opacity-75">Pilih salah satu kontak di sebelah kiri untuk melihat pesan<br>atau mulai menawar produk favorit Anda.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@if(isset($chat))
<script>
    const chatId = "{{ $chat->id }}";
    const msgContainer = document.getElementById('message-container');
    const chatInput = document.getElementById('chat-input');
    const inputWrapper = document.getElementById('input-wrapper');
    const editIndicator = document.getElementById('edit-indicator');
    const btnIcon = document.getElementById('btn-icon');
    
    let lastMsgId = "{{ $messages->last() ? $messages->last()->id : 0 }}";
    let editingMessageId = null;

    function scrollToBottom() { msgContainer.scrollTop = msgContainer.scrollHeight; }
    scrollToBottom();

    async function handleFormSubmit(e) {
        e.preventDefault();
        const text = chatInput.value.trim();
        if (!text) return;

        if (editingMessageId) {
            await updateMessage(editingMessageId, text);
        } else {
            await sendMessage(text);
        }
    }

    async function sendMessage(text) {
        chatInput.value = '';
        try {
            const response = await fetch(`{{ route('chat.send', $chat->id) }}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ message: text })
            });
            const data = await response.json();
            if (data.success) { fetchMessages(); }
        } catch (error) { console.error('Send error'); }
    }

    function enterEditMode(msgId, currentText) {
        editingMessageId = msgId;
        chatInput.value = currentText;
        chatInput.focus();
        inputWrapper.classList.add('edit-mode');
        editIndicator.classList.remove('d-none');
        btnIcon.className = 'fa fa-check';
    }

    function exitEditMode() {
        editingMessageId = null;
        chatInput.value = '';
        inputWrapper.classList.remove('edit-mode');
        editIndicator.classList.add('d-none');
        btnIcon.className = 'fa fa-paper-plane';
    }

    async function updateMessage(msgId, text) {
        try {
            const response = await fetch(`/chat/message/${msgId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ message: text })
            });
            const data = await response.json();
            if (data.success) {
                const textSpan = document.getElementById(`msg-text-${msgId}`);
                if (textSpan) textSpan.innerText = text;
                exitEditMode();
            }
        } catch (error) { console.error('Update error'); }
    }

    async function deleteMessage(msgId) {
        const result = await Swal.fire({
            title: 'Hapus Pesan?',
            text: "Pesan yang dihapus tidak bisa dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#9F1521',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            background: '#fff',
            borderRadius: '15px'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/chat/message/${msgId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                if (data.success) {
                    const wrapper = document.getElementById(`msg-wrapper-${msgId}`);
                    if (wrapper) {
                        wrapper.classList.add('msg-removing');
                        setTimeout(() => wrapper.remove(), 400);
                    }
                    
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Pesan dihapus',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            } catch (error) { 
                Swal.fire('Oops!', 'Gagal menghapus pesan.', 'error');
            }
        }
    }

    async function fetchMessages() {
        try {
            const response = await fetch(`{{ route('chat.messages', $chat->id) }}?after_id=${lastMsgId}`);
            const data = await response.json();
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (document.getElementById(`msg-wrapper-${msg.id}`)) return;
                    const isMe = msg.sender_id == data.current_user_id;
                    const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    
                    const wrapper = document.createElement('div');
                    wrapper.className = `bubble-wrapper ${isMe ? 'is-me' : 'is-other'}`;
                    wrapper.id = `msg-wrapper-${msg.id}`;
                    
                    const escapeHtml = (unsafe) => {
                        return (unsafe || "").toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                    };
                    
                    const escapedMsg = escapeHtml(msg.message);
                    const jsEscapedMsg = msg.message.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/\n/g, '\\n');
                    
                    let actionsHtml = isMe ? `<div class="bubble-actions"><span class="action-link" onclick="enterEditMode(${msg.id}, '${jsEscapedMsg}')"><i class="fa fa-pen"></i></span><span class="action-link action-delete" onclick="deleteMessage(${msg.id})"><i class="fa fa-trash"></i></span></div>` : '';

                    wrapper.innerHTML = `<div class="bubble"><span id="msg-text-${msg.id}">${escapedMsg}</span><small class="bubble-time">${time}</small></div>${actionsHtml}`;
                    msgContainer.appendChild(wrapper);
                    lastMsgId = msg.id;
                });
                scrollToBottom();
            }
        } catch (error) { console.error('Polling error'); }
    }

    setInterval(fetchMessages, 3500);
</script>
@endif
@endpush
