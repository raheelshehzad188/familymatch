<style type="">
  /* Add styles to center, border, box-shadow, padding, and rounded corners */
  .form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;          
    background-color: #f8f9fa; /* Light background for contrast */
  }
  .form-wrapper-full {
    max-width: 400px;     
    width: 100%;        
    padding: 20px;
    border: 2px solid #ddd; /* Light border from all sides */
    border-radius: 10px; /* Rounded corners for smooth look */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    background-color: #fff; /* White background for form */
  }
  .form-wrapper-full h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 26px;
    color: #333; /* Dark color for heading */
  }
  /* Optional: Style the Submit button to be more attractive */
  .btn-success {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }
  .btn-success:hover {
    background-color: #218838;
  }
  .col-md-6 {
    width: 50%;
}
</style>

<div class="form-container">
  <div class="form-wrapper-<?= $form_type ?>">
    <h2>Add <?= $label ?></h2>
    <form method="post" class="row" enctype="multipart/form-data">
        <?php
        foreach ($fields as $key => $value) {
            $file = dirname(__FILE__).'/fields/'.$value['type'].'.php';
            if($form_type == "half")
            {
            ?>
              <div class="col-md-6">
              <?php
            }

            if(file_exists($file)) {
                include $file;
            } else {
                $file = 'fields/default.php';
                include $file;
            }
            if($form_type == "half")
            {
            ?>
              </div>
            <?php
            }
        }
        ?>
        <button type="submit" class="btn btn-success mt-2">Submit</button>
    </form>
  </div>
</div>