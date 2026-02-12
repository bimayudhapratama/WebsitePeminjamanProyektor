<script>
var ctx = document.getElementById("chart-bars")?.getContext("2d");
if (ctx) {
  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["M","T","W","T","F","S","S"],
      datasets: [{
        label: "Views",
        backgroundColor: "#43A047",
        data: [50,45,22,28,50,60,76]
      }]
    },
    options: { responsive: true }
  });
}

var ctx2 = document.getElementById("chart-line")?.getContext("2d");
if (ctx2) {
  new Chart(ctx2, {
    type: "line",
    data: {
      labels: ["J","F","M","A","M","J","J","A","S","O","N","D"],
      datasets: [{
        label: "Sales",
        borderColor: "#43A047",
        data: [120,230,130,440,250,360,270,180,90,300,310,220]
      }]
    },
    options: { responsive: true }
  });
}
</script>
