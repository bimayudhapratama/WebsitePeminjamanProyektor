<div class="fixed-plugin">
  <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
    <i class="material-symbols-rounded py-2">settings</i>
  </a>

  <div class="card shadow-lg">
    <div class="card-header pb-0 pt-3">
      <h5>Material UI Configurator</h5>
      <button class="btn btn-link fixed-plugin-close-button text-dark p-0">
        <i class="material-symbols-rounded">clear</i>
      </button>
    </div>

    <div class="card-body">
      <h6>Sidebar Colors</h6>
      <div class="badge-colors">
        <span class="badge bg-gradient-primary" onclick="sidebarColor(this)"></span>
        <span class="badge bg-gradient-dark active" onclick="sidebarColor(this)"></span>
        <span class="badge bg-gradient-info" onclick="sidebarColor(this)"></span>
      </div>

      <hr>

      <h6>Light / Dark</h6>
      <input type="checkbox" onclick="darkMode(this)">
    </div>
  </div>
</div>
