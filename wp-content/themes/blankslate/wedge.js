setTimeout(function(){
const colors = ["#00E5FF", "#FC8B00", "#FF00B6", "#0BD10B", "#FFEF00"];

let numwedges;
if (window.innerWidth > 550) {
    numwedges = 57;
} else {
    numwedges = 20;
}
const wedges = [];

for (let i = 0; i < numwedges; i++) {
  let wedge = document.createElement("div");
  wedge.classList.add("wedge");
  wedge.style.background = colors[Math.floor(Math.random() * colors.length)];
  wedge.style.left = `${Math.min(85, Math.floor(Math.random() * 100))}vw`;
  wedge.style.top = `${Math.min(85, Math.floor(Math.random() * 100))}vh`;
  wedge.style.transform = `scale(${Math.random()})`;
  wedge.style.width = `${Math.random()}em`;
  wedge.style.height = wedge.style.width;
  wedge.style.opacity = 0;
  
  wedges.push(wedge);
  document.body.append(wedge);
}

// Keyframes
wedges.forEach((el, i, ra) => {
  let to = {
    x: Math.random() * (i % 2 === 0 ? -11 : 11),
    y: Math.random() * 12
  };

  let anim1 = el.animate(
    [
    { opacity: 0 },
    { opacity: 0.55 }
    ],
    {
    duration: 3000,
    fill: "both"
    }
    );

  let anim = el.animate(
    [
      { transform: "translate(0, 0)" },
      { transform: `translate(${to.x}rem, ${to.y}rem)` }
    ],
    {
      duration: (Math.random() + 1) * 2000, // random duration
      direction: "alternate",
      fill: "both",
      iterations: Infinity,
      easing: "ease-in-out"
    }
  );
});
}, 400);

document.addEventListener("DOMContentLoaded", function(){
    setTimeout(() => {
        document.querySelectorAll('.landing-container, .landing-text, .landing-video, .landing-frame, .landing, .texted, .quick-menu, .quick, .real-integration, .programs, .resources, .get-involved').forEach(el => el.style.opacity = 1);
    }, 100);
});