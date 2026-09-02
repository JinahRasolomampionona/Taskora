@php
    // Partial inclus avec : $task, $comments et, en option, $allowInternal.
    $allowInternal = $allowInternal ?? false;
@endphp

<x-panel title="Commentaires" class="mb-3">
    @can('comment', $task)
        <form action="{{ route('tasks.comments.store', $task) }}" method="POST"
              class="mb-3" data-tk-comment-form>
            @csrf

            <label for="comment-body" class="form-label">Ajouter un commentaire</label>
            <textarea id="comment-body" name="body" rows="3"
                      class="form-control @error('body') is-invalid @enderror"
                      placeholder="Précisez une information, posez une question, documentez une décision…"
                      style="min-height:88px"></textarea>
            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                @if ($allowInternal)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="comment-internal"
                               name="is_internal" value="1">
                        <label class="form-check-label tk-fs-sm" for="comment-internal">
                            Note interne (invisible pour le client)
                        </label>
                    </div>
                @else
                    <span></span>
                @endif

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-send me-1"></i>Publier
                </button>
            </div>
        </form>
    @endcan

    <div data-tk-comment-list>
        @forelse ($comments as $comment)
            <div class="tk-comment {{ $comment->is_internal ? 'tk-comment--internal' : '' }}">
                <x-avatar :user="$comment->user" size="sm" />
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-semibold tk-fs-sm">{{ $comment->user->name }}</span>
                        <x-role-badge :role="$comment->user->role" />
                        <span class="tk-fs-xs tk-text-faint">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        @if ($comment->is_internal)
                            <span class="tk-badge tk-badge-neutral">Note interne</span>
                        @endif
                    </div>
                    <div class="tk-comment__body">{{ $comment->body }}</div>
                </div>
            </div>
        @empty
            <div data-tk-comment-empty>
                <x-empty-state icon="bi-chat-left-text" title="Aucun commentaire"
                               text="Les échanges liés à cette tâche apparaîtront ici." small />
            </div>
        @endforelse
    </div>
</x-panel>
