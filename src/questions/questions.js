
function ajaxGet(endpointUrl, returnFunction){
    var xhr = new XMLHttpRequest();
    xhr.open('GET', endpointUrl, true);
    xhr.onreadystatechange = function(){
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                // When ajax call is complete, call this function, pass a string with the response
                returnFunction( xhr.responseText );
            } else {
                alert('AJAX Error.');
                console.log(xhr.status);
            }
        }
    }
    xhr.send();
};

function ajaxPost(endpointUrl, postdata, returnFunction){
    var xhr = new XMLHttpRequest();
    xhr.open('POST', endpointUrl, true);
    //Post requests also require somem information in the header, For example, the type of content that will be sent over
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function(){
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                returnFunction( xhr.responseText );
            } else {
                alert('AJAX Error.');
                console.log(xhr.status);
            }
        }
    }
    //in a POST reqeuest, need to send data separately like below, unlike get request
    xhr.send(postdata);
};

function changeQuestion(index) {
    // console.log("Change Question: " + index);
    //Check for valid index
    if(index < 0 || index > questions.length) {
        return;
    }
    question_index = index;
    //Update Text
    document.querySelector("#question").innerHTML = questions[index].question;
    document.querySelector("#btn-choice-1").textContent = questions[index].answer1;
    document.querySelector("#btn-choice-2").textContent = questions[index].answer2;
    //Update percents
    let answered = false;
    if(questions[question_index].response != -1) {
        answered = true;
    }
    if(answered) {
        let percent1 = 50;
        let percent2 = 50;
        let groupString = "";
        //Global
        if(group == -1) {
            groupString = "global";
        }
        //Friends
        else if(group == 0) {
            groupString = "friends";
        }
        //Groups
        else {
            groupString = "group" + group.toString();
        }
        //Math to calculate percentages
        let currentQuestion = questions[question_index];
        let answer1count = currentQuestion["groups"][groupString]["response1"];
        let answer2count = currentQuestion["groups"][groupString]["response2"];
        //Account for user response
        if(logged_in){
            if(currentQuestion.response == 1) {
                answer1count++;
            }
            else if (currentQuestion.response == 2) {
                answer2count++;
            }
        }
        let total = answer1count + answer2count;
        //Calculate percents
        percent1 = ((answer1count / total) * 100).toFixed(0);
        percent2 = ((answer2count / total) * 100).toFixed(0);
        if(total == 0) {
            percent1 = 50;
            percent2 = 50;
        }
        document.querySelector("#option-1-bar").textContent = percent1 + "%";
        document.querySelector("#option-1-bar").style.width = percent1 + "%";
        document.querySelector("#option-2-bar").textContent = percent2 + "%";
        document.querySelector("#option-2-bar").style.width = percent2 + "%";
        document.querySelector("#response-count").textContent = total;
        //Make bars striped
        if(currentQuestion.response == 1) { 
            document.querySelector("#option-1-bar").classList.add("progress-bar-striped", "progress-bar-animated");
            document.querySelector("#option-2-bar").classList.remove("progress-bar-striped", "progress-bar-animated");
        }else if(currentQuestion.response == 2) {
            document.querySelector("#option-2-bar").classList.add("progress-bar-striped", "progress-bar-animated");
            document.querySelector("#option-1-bar").classList.remove("progress-bar-striped", "progress-bar-animated");
        }
    }
    else {
        document.querySelector("#option-1-bar").textContent = "?%";
        document.querySelector("#option-1-bar").style.width = "50%";
        document.querySelector("#option-2-bar").textContent = "?%";
        document.querySelector("#option-2-bar").style.width = "50%";
        document.querySelector("#response-count").textContent = "?";
        document.querySelector("#option-1-bar").classList.remove("progress-bar-striped", "progress-bar-animated");
        document.querySelector("#option-2-bar").classList.remove("progress-bar-striped", "progress-bar-animated");
    }
}


let question_index = 0;
let logged_in = false;
let questions = [];
let group = -1;

ajaxGet("../api/question.php", function(results) {
    console.log(results); //DEBUG
    let objectResults = JSON.parse(results);
    console.log(objectResults); //DEBUG
    questions = objectResults.questions;
    logged_in = objectResults.logged_in;
    changeQuestion(0);
});

//Next button
document.querySelector("#btn-next-question").onclick = function() {
    next_index = (question_index + 1) % questions.length;
    document.querySelector("#transition-box").classList.add("moveLeft1");
    // changeQuestion(next_index);
}

//Prev button
document.querySelector("#btn-prev-question").onclick = function() {
    next_index = (question_index + questions.length - 1) % questions.length;
    document.querySelector("#transition-box").classList.add("moveRight1");
    // changeQuestion(next_index);
}

//Random button
document.querySelector("#btn-random-question").onclick = function() {
    next_index = question_index;
    while(next_index == question_index) {
        next_index = Math.floor(Math.random() * questions.length);
    }
    document.querySelector("#transition-box").classList.add("spin1");
    // changeQuestion(next_index);
}

//Selecting an option buttons
document.querySelector("#btn-choice-1").onclick = function() {
    if(logged_in) {
        let response = 1;
        let question_id = questions[question_index].id;
        let ajaxBody = "response=" + response +"&question_id=" + question_id;
        ajaxPost("../api/addresponse.php", ajaxBody, function(results) {
            console.log(results);
        });
    }
    questions[question_index].response = '1';
    changeQuestion(question_index);
}

document.querySelector("#btn-choice-2").onclick = function() {
    if(logged_in) {
        let response = 2;
        let question_id = questions[question_index].id;
        let ajaxBody = "response=" + response +"&question_id=" + question_id;
        ajaxPost("../api/addresponse.php", ajaxBody, function(results) {
            console.log(results);
        });
    }
    questions[question_index].response = '2';
    changeQuestion(question_index);
}

//Changing the group
if(document.querySelector("#form-group-selection") != null) {
    document.querySelector("#form-group-selection").onchange = function() {
        group = this.value;
        changeQuestion(question_index);
        // console.log(group);
    }
}

//Transitions
document.querySelector("#transition-box").onanimationend = function() {
    changeQuestion(next_index);
    let classes = this.classList;
    //Prev button
    if(classes.contains("moveRight2")) {
        classes.remove("moveRight2");
    }
    if(classes.contains("moveRight1")) {
        classes.remove("moveRight1");
        classes.add("moveRight2");
    }
    //Next button
    if(classes.contains("moveLeft2")) {
        classes.remove("moveLeft2");
    }
    if(classes.contains("moveLeft1")) {
        classes.remove("moveLeft1");
        classes.add("moveLeft2");
    }
    //Random button
    if(classes.contains("spin2")) {
        classes.remove("spin2");
    }
    if(classes.contains("spin1")) {
        classes.remove("spin1");
        classes.add("spin2");
    }
    
}