
  <style>
    .info-label {
      font-weight: bold;
    }
    .card {
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin-top: 30px;
    }
    .card-header {
      background-color: #337ab7;
      color: #fff;
      padding: 10px 15px;
      border-radius: 4px 4px 0 0;
      margin: -20px -20px 20px -20px;
    }
  </style>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <h4 class="text-center">Basic Info</h4>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">Name:</div>
        <div class="col-sm-9"><?= $user->full_name ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">DOB:</div>
        <div class="col-sm-9"><?= $user->dob ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">Gender:</div>
        <div class="col-sm-9"><?= $user->gender ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">Merital status:</div>
        <div class="col-sm-9"><?= $user->ms_namw ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">Height:</div>
        <div class="col-sm-9"><?= $user->height ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">Body Type:</div>
        <div class="col-sm-9"><?= $user->body ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">Reference:</div>
        <div class="col-sm-9"><?= $user->reffer ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">Religion:</div>
        <div class="col-sm-9"><?= $user->religion ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">Bio:</div>
        <div class="col-sm-9"><?= $user->bio ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">Country:</div>
        <div class="col-sm-9"><?= $user->country ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">State:</div>
        <div class="col-sm-9"><?= $user->state ?></div>
      </div>
      <div class="row">
        <div class="col-sm-3 info-label">City:</div>
        <div class="col-sm-9"><?= $user->city ?></div>
      </div>
    </div>
  </div>