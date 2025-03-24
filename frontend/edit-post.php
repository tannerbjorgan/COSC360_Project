<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../Backend/login.html");
    exit;
}

require_once '../Backend/config.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $post = $stmt->fetch();

    if (!$post) {
        header("Location: ../Backend/user-dashboard.php");
        exit;
    }
} catch (PDOException $e) {
    die("A database error occurred. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - Blogging Platform</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/edit-post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="edit-post-container">
        <div class="back-btn-container">
            <button onclick="window.location.href='../Backend/user-dashboard.php'" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>

        <div class="post-form-container">
            <h1>Edit Post</h1>
            <form id="editPostForm">
                <input type="hidden" id="postId" value="<?php echo htmlspecialchars($post['id']); ?>">
                
                <div class="form-group">
                    <label for="postTitle">Title</label>
                    <input type="text" id="postTitle" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="editor">Content</label>
                    <div class="editor-toolbar">
                        <button type="button" data-command="bold" title="Bold">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button type="button" data-command="italic" title="Italic">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button type="button" data-command="underline" title="Underline">
                            <i class="fas fa-underline"></i>
                        </button>
                        <button type="button" data-command="insertUnorderedList" title="Bullet List">
                            <i class="fas fa-list-ul"></i>
                        </button>
                        <button type="button" data-command="insertOrderedList" title="Numbered List">
                            <i class="fas fa-list-ol"></i>
                        </button>
                        <button type="button" data-command="createLink" title="Insert Link">
                            <i class="fas fa-link"></i>
                        </button>
                        <button type="button" data-command="removeFormat" title="Clear Formatting">
                            <i class="fas fa-remove-format"></i>
                        </button>
                    </div>
                    <div id="editor" class="editor-content" contenteditable="true"><?php echo $post['content']; ?></div>
                    <input type="hidden" id="postContent" name="content">
                </div>

                <div class="form-actions">
                    <button type="button" onclick="window.location.href='../Backend/user-dashboard.php'" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Post</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Rich Text Editor functionality
        const editor = document.getElementById('editor');
        const buttons = document.querySelectorAll('.editor-toolbar button');
        let lastSelection = null;

        // Save selection when clicking toolbar buttons
        editor.addEventListener('mouseup', function() {
            lastSelection = window.getSelection().getRangeAt(0);
        });

        // Handle toolbar button clicks
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const command = this.dataset.command;

                // Restore last selection
                if (lastSelection) {
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(lastSelection);
                }

                if (command === 'createLink') {
                    const url = prompt('Enter the URL:');
                    if (url) {
                        document.execCommand(command, false, url);
                    }
                } else {
                    document.execCommand(command, false, null);
                }

                // Update button active state
                if (['bold', 'italic', 'underline'].includes(command)) {
                    this.classList.toggle('active', document.queryCommandState(command));
                }

                // Focus back on editor
                editor.focus();
            });
        });

        // Keep toolbar buttons state updated
        editor.addEventListener('keyup', updateToolbar);
        editor.addEventListener('mouseup', updateToolbar);

        function updateToolbar() {
            buttons.forEach(button => {
                const command = button.dataset.command;
                if (['bold', 'italic', 'underline'].includes(command)) {
                    button.classList.toggle('active', document.queryCommandState(command));
                }
            });
        }

        // Form submission
        document.getElementById('editPostForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get content from editor and store in hidden input
            document.getElementById('postContent').value = editor.innerHTML;
            
            const postData = {
                post_id: document.getElementById('postId').value,
                title: document.getElementById('postTitle').value.trim(),
                content: document.getElementById('postContent').value.trim()
            };

            if (!postData.title || !postData.content) {
                alert('Please fill in all fields');
                return;
            }

            fetch('php/update_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '../Backend/user-dashboard.php';
                } else {
                    alert(data.error || 'Failed to update post');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update post');
            });
        });

        // Prevent accidental navigation
        window.addEventListener('beforeunload', function(e) {
            const content = editor.innerHTML;
            const title = document.getElementById('postTitle').value;
            if (content !== '<?php echo addslashes($post['content']); ?>' || 
                title !== '<?php echo addslashes($post['title']); ?>') {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Initialize editor with focus
        editor.focus();
    </script>
</body>
</html> 