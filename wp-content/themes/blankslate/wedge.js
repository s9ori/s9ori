setTimeout(function(){
const colors = ["#d4acff", "#7ab8ff", "#ffffff"];

let numwedges;
if (window.innerWidth > 550) {
    numwedges = 20;
} else {
    numwedges = 5;
}
const wedges = [];

for (let i = 0; i < numwedges; i++) {
  let wedge = document.createElement("div");
  wedge.classList.add("wedge");
  wedge.style.background = colors[Math.floor(Math.random() * colors.length)];
  wedge.style.left = `${Math.min(85, Math.floor(Math.random() * 100))}vw`;
  wedge.style.top = `${Math.min(85, Math.floor(Math.random() * 100))}vh`;
  wedge.style.transform = `scale(${Math.random()})`;
  wedge.style.width = `${Math.min(15, Math.floor(Math.random() * 30))}em`;
  wedge.style.height = wedge.style.width;
  let backgroundColor = wedge.style.background;
  wedge.style.boxShadow = `0px 0px 50px 50px ${backgroundColor}`;
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