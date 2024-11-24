document.addEventListener("DOMContentLoaded", function () {
  const questions = JSON.parse(mac_ai_data.questions);
  const questionKeys = Object.keys(questions);
  let currentQuestionIndex = 0;
  const answers = {};

  const questionLabel = document.getElementById("mac-ai-question-label");
  const answerInput = document.getElementById("mac-ai-answer-input");
  const answerOptions = document.getElementById("mac-ai-answer-options");
  const submitButton = document.getElementById("mac-ai-submit-answer");
  const generateButton = document.getElementById("mac-ai-generate-answer");

  // Load the current question
  function loadQuestion() {
    const questionKey = questionKeys[currentQuestionIndex];
    const question = questions[questionKey];

    if (!question) {
      // All questions answered
      document.getElementById("mac-ai-form").innerHTML =
        "<h3>Your Answers:</h3><pre>" +
        JSON.stringify(answers, null, 2) +
        "</pre>";
      return;
    }

    // Set question label
    if (typeof question === "string") {
      questionLabel.textContent = question;
      answerInput.style.display = "block";
      answerOptions.style.display = "none";
      answerInput.value = ""; // Clear previous input
      generateButton.disabled = false; // Enable "Generate with AI"
    } else {
      questionLabel.textContent = question.label;
      answerInput.style.display = "none";
      answerOptions.style.display = "block";
      answerOptions.innerHTML = question.options
        .map((opt) => `<option value="${opt}">${opt}</option>`)
        .join("");
      generateButton.disabled = true; // Disable "Generate with AI" for dropdown
    }
  }

  // Handle "Submit My Answer" button click
  submitButton.addEventListener("click", function () {
    const questionKey = questionKeys[currentQuestionIndex];
    answers[questionKey] =
      answerInput.style.display === "block"
        ? answerInput.value
        : answerOptions.value;

    currentQuestionIndex++;
    loadQuestion();
  });

  // Handle "Generate with AI" button click
  generateButton.addEventListener("click", function () {
    const questionKey = questionKeys[currentQuestionIndex];
    generateButton.disabled = true; // Prevent multiple clicks

    fetch(mac_ai_data.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "mac_ai_generate_answer",
        question: questionLabel.textContent,
        api_key: mac_ai_data.api_key,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          answerInput.value = data.data.response; // Populate the input field
        }
        generateButton.disabled = false; // Re-enable button
      })
      .catch(() => {
        alert("An error occurred while generating the response.");
        generateButton.disabled = false; // Re-enable button
      });
  });

  loadQuestion();
});
