<div class="col-lg-4 col-md-6">
  <div class="card h-100">
    <div class="card-header pb-0">
      <h6>Orders overview</h6>
      <p class="text-sm">
        <i class="fa fa-arrow-up text-success"></i>
        <span class="font-weight-bold">24%</span> this month
      </p>
    </div>

    <div class="card-body p-3">
      <div class="timeline timeline-one-side">

        <?php
        $orders = [
          ["icon"=>"notifications","color"=>"success","title"=>"$2400, Design changes","time"=>"22 DEC 7:20 PM"],
          ["icon"=>"code","color"=>"danger","title"=>"New order #1832412","time"=>"21 DEC 11 PM"],
          ["icon"=>"shopping_cart","color"=>"info","title"=>"Server payments for April","time"=>"21 DEC 9:34 PM"],
          ["icon"=>"credit_card","color"=>"warning","title"=>"New card added for order #4395133","time"=>"20 DEC 2:20 AM"],
          ["icon"=>"key","color"=>"primary","title"=>"Unlock packages for development","time"=>"18 DEC 4:54 AM"],
          ["icon"=>"payments","color"=>"dark","title"=>"New order #9583120","time"=>"17 DEC"]
        ];

        foreach ($orders as $o): ?>
          <div class="timeline-block mb-3">
            <span class="timeline-step">
              <i class="material-symbols-rounded text-<?= $o['color'] ?> text-gradient"><?= $o['icon'] ?></i>
            </span>
            <div class="timeline-content">
              <h6 class="text-dark text-sm font-weight-bold mb-0"><?= $o['title'] ?></h6>
              <p class="text-secondary font-weight-bold text-xs mt-1 mb-0"><?= $o['time'] ?></p>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </div>
</div>
