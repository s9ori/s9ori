const playButton = document.getElementById("play");
const exerciseButton = document.getElementById("exercise");
const dropsDiv = document.getElementById("drops");

window.addEventListener("load", () => {
    retrievePetState();
    pet.level = getLevel(pet.powerPoints);
    updatePetState(pet);
    const levelElement = document.getElementById("level");
    levelElement.textContent = `Level: ${pet.level}`;
    updateLevel();
  });

const levelElement = document.getElementById("level");
const adventureButton = document.getElementById("adventure");
const foodsDiv = document.getElementById("foods");
const monstersDiv = document.getElementById("monsters");

// Define pet object with default values
let pet = {
  mood: 25,
  fitness: 25,
  powerPoints: 0,
  moodRibbon: 0,
  fitnessRibbon: 0,
  adventureRibbon: 0
};

const LEVEL_CAP = 99;

// Define point values for interactions
const INTERACTION_POINTS = {
  PLAY: {
    mood: 10 + pet.moodRibbon,
    powerPoints: () => getRandompowerPoints()
  },
  FEED: {
    fitness: -5,
    mood: 5,
    powerPoints: 5
  },
  EXERCISE: {
    fitness: 10 + pet.fitnessRibbon,
    mood: -5,
    powerPoints: 10
  },
  ADVENTURE: {
    fitness: 10,
    mood: 10,
    powerPoints: () => getRandompowerPoints()
  }
};

function getInteractionPoints() {
    const updatedPoints = {
      PLAY: {
        mood: 10 + pet.moodRibbon,
        powerPoints: () => getRandompowerPoints(),
      },
      FEED: {
        fitness: -5,
        mood: 5,
        powerPoints: 5
      },
      EXERCISE: {
        fitness: 10 + pet.fitnessRibbon,
        mood: -5,
        powerPoints: 10
      },
      ADVENTURE: {
        fitness: 10,
        mood: 10,
        powerPoints: () => getRandompowerPoints()
      }
    };
    return updatedPoints;
  }
  

// Define function to calculate experience required for a given level
function getExperienceForLevel(level) {
    if (level <= 1) {
      return 0;
    } else {
      return Math.floor(getExperienceForLevel(level - 1) + (level - 1) + 300 * Math.pow(2, (level - 1) / 7));
    }
  }
  
  // Define function to get the pet's current level based on its power level
  function getLevel(powerPoints) {
    let level = 1;
    let experience = 0;
    while (level <= LEVEL_CAP && experience <= powerPoints) {
      experience += getExperienceForLevel(level);
      level++;
    }
    return level - 1;
  }

function getRandompowerPoints() {
    return Math.floor(Math.random() * 2) * 5;
  }

  playButton.addEventListener("click", playWithPet);
  exerciseButton.addEventListener("click", exercisePet);
  adventureButton.addEventListener("click", adventurePet);
  

  // Define function to decrease pet's mood and fitness levels by random amount
function decreaseMoodAndFitness() {
    pet.mood -= Math.floor(Math.random() * 10);
    pet.fitness -= Math.floor(Math.random() * 10);
  }
  
  // Set interval to decrease mood and fitness levels every 10 seconds
  setInterval(() => {
    decreaseMoodAndFitness();
    updatePetState(pet);
    savePetState();
  }, 10000);

  function updateLevel() {
    const newLevel = getLevel(pet.powerPoints);
    if (newLevel !== pet.level) {
      pet.level = newLevel;
      document.getElementById("level").textContent = `Level: ${pet.level}`;
    }
  }

// Set interval to add new elements with a probability that depends on the pet's level
setInterval(() => {
    const foodsDiv = document.getElementById("foods");
    const level = pet.level;
  
    // Define probabilities for each type of food as a function of the pet's level
    let rareProbability = level / (1000);  // Becomes rarer as level goes up
    let uncommonProbability = (1) / (30);  // Becomes rarer as level goes up
    let commonProbability = (1) / (15);  // Becomes rarer as level goes up

  
    // Generate a random number and add a food element based on the probability
    let rand = Math.random();
    if (rand < rareProbability) {
      addElement("rare");
    } else if (rand < uncommonProbability) {
      addElement("uncommon");
    } else if (rand < commonProbability) {
      addElement("common");
    }    
  }, 1000);
  
function addElement(type) {
  let moodBoost, imageSrc;
  if (type === "rare") {
    moodBoost = 150;
    imageSrc = 'https://lowfemme.com/wp-content/uploads/2023/02/Asset-2.png';
  } else if (type === "uncommon") {
    moodBoost = 100;
    imageSrc = 'https://lowfemme.com/wp-content/uploads/2023/02/Asset-1.png';
  } else {
    moodBoost = 50;
    imageSrc = 'https://lowfemme.com/wp-content/uploads/2023/02/Asset-3.png';
  }

    const img = document.createElement('img');
    img.src = imageSrc;
    img.alt = 'Image';
    img.width = 38;
  
    const newElement = document.createElement('div');
    newElement.appendChild(img);
    newElement.classList.add('food');
  
    newElement.addEventListener('click', () => {
      pet.mood += moodBoost;
      updatePetState(pet);
      newElement.remove();
    });
  
    const foodsDiv = document.getElementById('foods');
    foodsDiv.appendChild(newElement);
  }  

  // Set interval to add new monsters with a probability that depends on the pet's level
setInterval(() => {
    const monstersDiv = document.getElementById("monsters");
    const level = pet.level;
  
    // Define probabilities for each type of food as a function of the pet's level
    let monsterProbability = (1) / (10);  // Becomes rarer as level goes up
  
    // Generate a random number and add a food element based on the probability
    let rand = Math.random();
    if (rand < monsterProbability) {
      addMonster("monster");
    }
  }, 1000);



  function dropElement(type) {
    let imageSrc;
    if (type === "ribbon1") {
      imageSrc = 'https://lowfemme.com/wp-content/uploads/2023/02/tumblr_2dd2dd3e0bc9407e8e0d1a3b01c67b38_4b38d417_75.webp';
    } else if (type === "ribbon2") {
      imageSrc = 'https://lowfemme.com/wp-content/uploads/2023/02/tumblr_bd16179ec8017844f4175a144f1b6a2c_5a6b991f_75.webp';
    } else if (type === "ribbon3") {
      imageSrc = 'https://lowfemme.com/wp-content/uploads/2023/02/tumblr_9acc2ace0bf9920ded8a4ef9a1be77ee_c56fb2cd_75.webp';
    }
  
    const img = document.createElement('img');
    img.src = imageSrc;
    img.alt = 'Ribbon';
    img.width = 18;
  
    const newElement = document.createElement('div');
    newElement.appendChild(img);
    newElement.classList.add('drop');
  
    newElement.addEventListener('click', () => {
      if (type === "ribbon1") {
        pet.fitnessRibbon += 1; // add 1 to the fitnessRibbon stat when ribbon1 is clicked
      } else if (type === "ribbon2") {
        pet.moodRibbon += 1; // add 1 to the moodRibbon stat when ribbon2 is clicked
      } else if (type === "ribbon3") {
        pet.adventureRibbon += 1; // add 1 to the adventureRibbon stat when ribbon3 is clicked
      }
      updatePetState(pet);
      newElement.remove();
    });

  const dropsDiv = document.getElementById('drops');
  dropsDiv.appendChild(newElement);
}
  
  function addMonster(type) {
    let moodBoost, imageSrc;
    if (type === "monster") {
      let monsterLevel = Math.floor(Math.random() * 50) + 1;
      const monsterImg = document.createElement('img');
      monsterImg.src = 'https://lowfemme.com/wp-content/uploads/2023/02/tumblr_inline_p7gi2483iO1qfc9y0_75sq.gif';
      monsterImg.alt = 'Monster';
      monsterImg.width = 38;
  
      const monsterLevelEl = document.createElement('p');
      monsterLevelEl.innerText = `Level: ${monsterLevel}`;
      monsterLevelEl.classList.add('monster-level');
  
      const monsterElement = document.createElement('div');
      monsterElement.appendChild(monsterImg);
      monsterElement.appendChild(monsterLevelEl);
      monsterElement.classList.add('monster');
      monsterElement.addEventListener('click', () => {
        const winChance = pet.level / monsterLevel;
        const rand = Math.random();
        if (rand < winChance) {
          const powerPointsWon = monsterLevel * 15;
          pet.powerPoints += powerPointsWon;
          updatePetState(pet);
          monsterElement.remove();
          const alertEl = document.createElement('p');
          alertEl.innerText = `You won the dance battle! + ${powerPointsWon} power points.`;
          alertEl.classList.add('alert');
          monstersDiv.appendChild(alertEl);
          setTimeout(() => {
            alertEl.remove();
            const rollChance = Math.random();
          if (rollChance < 0.5) {
            dropElement("ribbon1");
          } else if (rollChance < 0.8) {
            dropElement("ribbon2");
          } else {
            dropElement("ribbon3");
          }
          }, 1000);
        } else {
          pet.fitness = 0;
          pet.mood = 0;
          updatePetState(pet);
          monsterElement.remove();
          const alertEl = document.createElement('p');
          alertEl.innerText = 'You lost the dance battle! Your fitness and mood are drained to 0.';
          alertEl.classList.add('alert');
          monstersDiv.appendChild(alertEl);
          setTimeout(() => {
            alertEl.remove();
          }, 2000);
        }
      });
      // Remove any existing monster elements
      while (monstersDiv.firstChild) {
        monstersDiv.removeChild(monstersDiv.firstChild);
      }
      monstersDiv.appendChild(monsterElement);
      return;
    }
  
    const img = document.createElement('img');
    img.src = imageSrc;
    img.alt = 'Image';
    img.width = 38;
  
    const newElement = document.createElement('div');
    newElement.appendChild(img);
    newElement.classList.add('monster');

    monstersDiv.appendChild(newElement);
  }

  setInterval(updateLevel, 1000);
// Define function to update pet state and save to local storage
function updatePetState(newState) {
    pet = { ...pet, ...newState };
    const newLevel = getLevel(pet.powerPoints);
    if (newLevel !== pet.level) {
        pet.level = newLevel;
        document.getElementById("level").textContent = `Level: ${pet.level}`;
      }
    localStorage.setItem("petState", JSON.stringify(pet));
 // Update DOM elements with current state levels
 document.getElementById("mood-state").textContent = pet.mood;
 document.getElementById("fitness-state").textContent = pet.fitness;
 document.getElementById("power-level").textContent = pet.powerPoints;
 document.getElementById("moodRibbon-state").textContent = pet.moodRibbon;
 document.getElementById("fitnessRibbon-state").textContent = pet.fitnessRibbon;
 document.getElementById("adventureRibbon-state").textContent = pet.adventureRibbon;
}

// Define function to retrieve pet state from local storage
function retrievePetState() {
    const storedPetState = localStorage.getItem("petState");
    if (storedPetState) {
        pet = JSON.parse(storedPetState);
    }
 // Update DOM elements with current state levels
 document.getElementById("mood-state").textContent = pet.mood;
 document.getElementById("fitness-state").textContent = pet.fitness;
 document.getElementById("power-level").textContent = pet.powerPoints;
 document.getElementById("moodRibbon-state").textContent = pet.moodRibbon;
 document.getElementById("fitnessRibbon-state").textContent = pet.fitnessRibbon;
 document.getElementById("adventureRibbon-state").textContent = pet.adventureRibbon;
}

function playWithPet() {
    const points = getInteractionPoints();
    pet.mood += parseInt(points.PLAY.mood) || 0;
    pet.powerPoints += parseInt(points.PLAY.powerPoints()) || 0;
    updatePetState(pet);
}

function exercisePet() {
    const points = getInteractionPoints();
    if (pet.mood >= 0) {
      pet.fitness += parseInt(points.EXERCISE.fitness) || 0;
      pet.mood += parseInt(points.EXERCISE.mood) || 0;
      pet.powerPoints += parseInt(points.EXERCISE.powerPoints) || 0;  
      updatePetState(pet);
    }
    }
  

    function adventurePet() {
        if (pet.mood > 0 && pet.fitness > 0) {
          // Determine power level increment based on mood and fitness levels
          let powerPointsDelta = 0;
          const fitnessModifier = Math.floor(pet.fitness / 10); // value between -10 and 10
          const moodModifier = Math.floor(pet.mood / 10); // value between -10 and 10
          const randomIncrement = Math.floor(Math.random() * 11); // random value between 0 and 10
          powerPointsDelta = randomIncrement + fitnessModifier + moodModifier;
      
          if (powerPointsDelta < 0) {
            powerPointsDelta = 0;
          }
      
          // Decrease mood and fitness levels by random amount
          const moodDecrease = Math.floor(Math.random() * -101);
          const fitnessDecrease = Math.floor(Math.random() * -101);
          pet.mood += moodDecrease;
          pet.fitness += fitnessDecrease;
      
          pet.powerPoints += powerPointsDelta;
          updatePetState(pet);
        } else {
          console.log("Pet's mood or fitness level is too low for an adventure.");
        }
      }
      
  
function savePetState() {
    localStorage.setItem("petState", JSON.stringify(pet));
  }