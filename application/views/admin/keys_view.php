<style>
  /* Custom styling for a cleaner look */
  body {
    background-color: #f8f9fa;
    font-family: 'Arial', sans-serif;
  }

  .container {
    max-width: 100%;
    margin: 50px auto;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  }

  h4 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
  }

  /* Input fields smaller */
  .form-control {
    height: 40px;
    font-size: 14px;
  }

  /* Buttons styling */
  .btn-primary {
    background-color: #007bff;
    border: none;
  }
  .btn-xs {
    padding: 4px 8px;
    font-size: 12px;
  }

  /* Table formatting */
  table {
    width: 100%;
    border-collapse: collapse;
  }
  th, td {
    padding: 8px;
    text-align: center;
  }
  tr:nth-child(even) {
    background-color: #f2f2f2;
  }
  form{
        width: 41%;
    margin-left: 326px;
  }\
  table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 8px; /* Space between rows for a softer look */
  border-radius: 8px;
  overflow: hidden; /* Rounded corners */
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  background-color: #fff;
}

thead {
  background-color: #333b42;
  color: #fff;
}

thead th {
  padding: 12px;
  font-weight: bold;
}

tbody tr {
  background-color: #fff;
  transition: all 0.2s ease;
  border-radius: 8px;
}

tbody tr:hover {
  background-color: #f1f1f1;
  transform: translateY(-2px);
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

td {
  padding: 10px;
  text-align: center;
}

/* Remove default border for cleaner look */
table th, table td {
  border: none;
}
.new_btn{
    width: 22% !important;
    margin-left: 165px;
}
</style>

<div class="container">
  <h4>API Keys Management</h4>
  
  <form method="POST" class="mb-4">
    <?php if (isset($msg)) echo $msg; ?>
    <div class="mb-3">
      <input type="text" class="form-control" name="host" placeholder="Host" required>
    </div>
    <div class="mb-3">
      <input type="text" class="form-control" name="key" placeholder="API Key" required>
    </div>
    <button type="submit" class="btn new_btn btn-primary ">Save Key</button>
  </form>

  <h4>Saved API Keys</h4>
  

<table class="table">
  <thead>
    <tr>
      <th>#</th>
      <th>Host</th>
      <th>API Key</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($keys)) :
      $i = 1;
      foreach ($keys as $row) : ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row->host) ?></td>
          <td>
            <input type="text" class="form-control" id="key<?= $row->id ?>" value="<?= htmlspecialchars($row->key) ?>" readonly style="width: 100px; display: inline-block;">
          </td>
          <td>
            <button class="btn btn-sm btn-info" onclick="copyKey('key<?= $row->id ?>')">Copy</button>
            <a href="<?= base_url('admin/admin_keys/delete/' . $row->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this key?')">Remove</a>
          </td>
        </tr>
    <?php endforeach; ?>
    <?php else : ?>
      <!-- No Data message, styling preserved -->
      <tr>
        <td colspan="4" style="background-color:#eef1f5; border-radius:8px; padding:20px;">
          <div style="display:flex; align-items:center; justify-content:center; gap:10px; font-size:20px; color:#555;">
            <!-- Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#888" viewBox="0 0 24 24">
              <path d="M12 0C5.372 0 0 5.372 0 12s5.372 12 12 12 12-5.372 12-12S18.628 0 12 0zm0 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-7h2v-2h-2v2zm2-4h-2V7h2v4z"/>
            </svg>
            <span>No API keys found. Please add a new key above.</span>
          </div>
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<script>
function copyKey(id) {
  const copyText = document.getElementById(id);
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  try {
    document.execCommand('copy');
    alert('Copied the key: ' + copyText.value);
  } catch (err) {
    alert('Failed to copy');
  }
}
</script>