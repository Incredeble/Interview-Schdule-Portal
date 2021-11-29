// // ******************************************* Loading of data*************************************

document.addEventListener('DOMContentLoaded', function () {
    $.ajax({
        url:"getAll.php",
        method:"POST",
        success:function(data)
        {
            console.log(data);
            data = JSON.parse(data);
            loadDropdownList(data);
        },
        error: function(error) {
            alert("error in loading data");
        }
    });

    $.ajax({
        url:"getAllInterview.php",
        method:"POST",
        success:function(data)
        {
            console.log(data);
            data = JSON.parse(data);
            loadInterviewTable(data);
        },
        error: function(error) {
            alert("error in loading getAllInterview data");
        }
    });
});


function loadDropdownList(data) {
    const drop1 = document.querySelector('#interviewer-name');
    const drop2 = document.querySelector('#interviewee-name');

    let dropdownHtml = "";

    data.forEach(function ({name, email_id}) {
        
        const dataToStore = name + '(' + email_id + ')';
        dropdownHtml += `<option value="${dataToStore}">${dataToStore}</option>`
    });

    drop1.innerHTML = dropdownHtml;
    drop2.innerHTML = dropdownHtml;
}

function loadInterviewTable(data) {
    const table = document.querySelector('table tbody');

    if (data.length === 0) {
        table.innerHTML = "<tr><td class='no-data' colspan='7'>No Data</td></tr>";
        return;
    }

    let tableHtml = "";

    data.forEach(function ({id, email1, email2, startTime, endTime}) {
        
        tableHtml += "<tr>";
        tableHtml += `<td>${id}</td>`;
        tableHtml += `<td>${email1}</td>`;
        tableHtml += `<td>${email2}</td>`;
        tableHtml += `<td>${new Date(startTime).toLocaleString()}</td>`;
        tableHtml += `<td>${new Date(endTime).toLocaleString()}</td>`;
        const dataToStore = `${id},${email1},${email2}`;
        tableHtml += `<td><button class="delete-row-btn" data-id=${id}>Delete</td>`;
        tableHtml += `<td><button class="edit-row-btn" data-id=${dataToStore}>Edit</td>`;
        tableHtml += "</tr>";
    });

    table.innerHTML = tableHtml;
}

convertDateTime = (datetime) => {
    datetime = datetime.split(' ');
    date = datetime[0].split('/');
    time = datetime[1].split(':');
    mer = datetime[2];
    if (mer == 'PM') {
        if(time[0] !== '12') {
            hh = parseInt(time[0]);
            hh+=12;
            time[0] = hh.toString();
        } 
    }
    else {
        if(time[0]==='12') {
            time[0]='00';
        }
    }
    sqlDate = "";
    sqlDate += date[2] + '-' + date[0] + '-' + date[1] + ' ' + time[0] + ':' + time[1] + ':' + '00';
    return sqlDate;
}

// // ******************************************* Handling of edit and delete operations *************************************

document.querySelector('table tbody').addEventListener('click', function(event) {
    if (event.target.className === "delete-row-btn") {
        deleteInterviewById(event.target.dataset.id);
    }
    if (event.target.className === "edit-row-btn") {
        handleEditInterview(event.target.dataset.id);
    }
});

function deleteInterviewById(id) {
    $.ajax({
        url:"deleteInterview.php",
        method:"POST",
        data:{id:id},
        success:function(data)
        {
            alert(data);
            location.reload();
        },
        error: function(error) {
            alert("error in loading getAllInterview data");
        }
    });
}

const updateBtn = document.querySelector('#update-row-btn');

function handleEditInterview(id) {
    event.preventDefault();
    const updateSection = document.querySelector('#update-row');
    updateSection.hidden = false;
    document.querySelector('#start-time-updated').dataset.id = id;
}

updateBtn.onclick = function() {
    event.preventDefault();
    const updateDate1 = document.querySelector('#start-time-updated');
    const updateDate2 = document.querySelector('#end-time-updated');
    const data = updateDate1.dataset.id.split(',');

    if(updateDate1.value === "" || updateDate2.value === "") {
        alert("Select Date and Time");
        return;
    }
    
    const startTime = convertDateTime(updateDate1.value);
    const endTime = convertDateTime(updateDate2.value);
    const id = data[0];
    const email1 = data[1];
    const email2 = data[2];

    $.ajax({
        url:"updateInterview.php",
        method:"POST",
        data:{id:id,email1:email1,email2:email2,startTime:startTime,endTime:endTime},
        success:function(data)
        {   
            console.log(data);
            data = JSON.parse(data);
            alert(data.id);
            updateVerdict(data);
        },
        error: function(error) {
            alert("error in updating interview schdule");
        }
    });
}

function updateVerdict(data) {
    if (data.id===-1) {
        alert("Interviewer Not available at that time");
    } 
    else if (data.id===-2) {
        alert("Interviewee Not available at that time");
    }
    else {
        location.reload();
    }
}

// ******************************************* Scheduling new interview *************************************



const submitButton = document.querySelector('#submit-btn');

submitButton.onclick = function () {
    const email1 = document.querySelector("#interviewer-name").value;
    const email2 = document.querySelector("#interviewee-name").value;
    const start = document.querySelector("#start-time").value;
    const end = document.querySelector("#end-time").value;
    
    //console.log(email1, email2, startTime,endTime);

    if(email1 === email2) {
        alert("Interviewer and Interviewee cannot be same");
        return;
    }
    if(start === "" || end === "") {
        alert("Select Date and Time");
        return;
    }
    const startTime = convertDateTime(start);
    const endTime = convertDateTime(end);
    function asyncAjax() {
        return new Promise(function(resolve,reject) {
            $.ajax({
                url:"insertInterview.php",
                method:"POST",
                data: {email1:email1,email2:email2,startTime:startTime,endTime:endTime},
                success:function(data)
                {
                    resolve(data);
                },
                error: function(error) {
                    reject(error);
                }
            });
        });
    }
    async function doAjax() {
        try {
            var data = await asyncAjax();
            data = JSON.parse(data);
            insertRowIntoInterviewTable(data);
            location.reload();
            
        }
        catch (e) {
            console.log(e);
        }
    }
    doAjax();
    
}

function insertRowIntoInterviewTable(data) {
    if(data.id === -1) {
        alert("Interviewer Not available at that time");
        return;
    }
    if(data.id === -2) {
        alert("Interviewee Not available at that time");
        return;
    }
    const table = document.querySelector('table tbody');
    const isTableData = table.querySelector('.no-data');

    let tableHtml = "<tr>";

    for (var key in data) {
        if (data.hasOwnProperty(key)) {
            if (key === 'startTime' || key==='endTime') {
                data[key] = new Date(data[key]).toLocaleString();
            }
            tableHtml += `<td>${data[key]}</td>`;
        }
    }
    const dataToStore = `${data.id},${data.email1},${data.email2}`;
    tableHtml += `<td><button class="delete-row-btn" data-id=${data.id}>Delete</td>`;
    tableHtml += `<td><button class="edit-row-btn" data-id=${dataToStore}>Edit</td>`;

    tableHtml += "</tr>";

    if (isTableData) {
        table.innerHTML = tableHtml;
    } else {
        const newRow = table.insertRow();
        newRow.innerHTML = tableHtml;
    }
}
