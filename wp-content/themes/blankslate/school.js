// define the schoolData array
let schoolData = [];

// define a variable to hold the pie chart object
let pieChart = undefined;

async function fetchData() {
  // set the URL of the API
  let labeling = ['Black', 'Asian', 'White', 'Latino'];
  let API_URL = "https://data.cityofnewyork.us/resource/c7ru-d68s.json";

  // get the selected school name from the form
  let select = document.getElementById('school_name');
  let schoolName = select.value.replace(/&amp;/g, '&');

  // do not attempt to fetch data if no school name has been selected
  if (!schoolName) {
    return;
  }

  // create the query string for the API
  let query = `Year=2020-21&school_name=${encodeURIComponent(schoolName)}`;

  // clear the schoolData array
  schoolData.length = 0;

  try {
    // fetch the data from the API
    const response = await fetch(`${API_URL}?${query}`);
    const data = await response.json();

    // find the selected school's data in the array of data
    let item = data.find(i => i.school_name === schoolName);
    let schoolData = [];
    data.forEach(item => {
      schoolData.push(item.black);
      schoolData.push(item.asian);
      schoolData.push(item.white);
      schoolData.push(item.hispanic);
    });

    // check if a pie chart with the specified canvas element already exists
    if (typeof pieChart !== 'undefined') {
      // clear the canvas and destroy the old pie chart
      pieChart.clear();
      pieChart.destroy();
    }

    function createPieChart(schoolData) {
      const ctx = document.getElementById('myPieChart').getContext('2d');
      // get the myPieChart element
      let myPieChart = document.getElementById('myPieChart');

      // modify the element's style attribute to set its opacity and top properties
      myPieChart.style.setProperty('opacity', 1);
      myPieChart.style.setProperty('top', 0);

      pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
          datasets: [{
            data: schoolData,
            backgroundColor: [
              '#B20966',
              '#691FE0',
              '#B792F1',
              '#1C1A36'
            ],
            borderColor: [
              '#B20966',
              '#691FE0',
              '#B792F1',
              '#1C1A36'
            ]
          }],
          labels: labeling
        },
        options: {
          responsive: true,
          aspectRatio: 1,
          legend: false,
          title: {
            display: false,
            text: 'My Pie Chart'
          }
        }
      });
    }

    window.scrollTo({
      top: 450,
      left: 0,
    behavior: 'smooth',
    duration: 9000 // 3 seconds in milliseconds
    });



    // call the createPieChart() function using the schoolData array as an argument
    createPieChart(schoolData);
    }
    catch (error) {
        // handle the error here
        console.error(error)};
    }
    