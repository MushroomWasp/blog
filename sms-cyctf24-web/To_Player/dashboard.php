<?php
include 'db.php';

session_start();

if (!$_SESSION['admin'])
{
	header('Location: login.php');
	die();
}

$stmt = $pdo->prepare("SELECT * FROM uploads");
$stmt->execute();
$uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h2 class="my-4">Uploaded Files</h2>
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>User Supplied Hash</th>
                        <th>Uploaded At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($uploads as $upload): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($upload['file_name']); ?></td>
                            <td><?php echo htmlspecialchars($upload['user_hash']); ?></td>
                            <td><?php echo htmlspecialchars($upload['uploaded_at']); ?></td>
                            <td>
                                <button class="btn btn-warning" onclick="checkIntegrity('<?php echo $upload['file_name']; ?>', '<?php echo $upload['user_hash']; ?>')">Check Integrity</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
    <h2 class="my-4">Submitted Feedback</h2>
    <div class="feedback-section text-dark">
        <ul class="list-group" id="feedback-list">
        </ul>
    </div>
</div>


        </div>
    </div>
</div>


<script>
    function fetchFeedback() {
        fetch('feedback.php') 
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json(); 
            })
            .then(data => {
                const feedbackList = document.getElementById('feedback-list');
                feedbackList.innerHTML = '';
                
                data.forEach(feedback => {
                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    listItem.innerHTML = `
                        Feedback from ${feedback.username} - ${feedback.feedback}
                        <input type="checkbox" />
                    `;
                    feedbackList.appendChild(listItem);
                });
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', fetchFeedback);
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script>
function checkIntegrity(fileName, userHash) {
    $.ajax({
        url: 'check_integrity.php', 
        type: 'POST',
        data: { file_name: fileName, user_hash: userHash },
        success: function(response) {
            alert(response); 
        },
        error: function(xhr, status, error) {
            alert("An error occurred: " + error);
        }
    });
}
</script>

</body>
</html>

