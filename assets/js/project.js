var xhttp = new XMLHttpRequest();

function deleteProject(id)
{
    var status = confirm("Do you really want to delete this project?");
    if (status)
    {
        xhttp.onreadystatechange = function()
        {
            if (this.readyState == 4)
            {
                var answer = JSON.parse(this.responseText);
                if (answer['error'] != undefined)
                    alert(answer['error']);
                else
                {
                    window.location.href = '../Projects/';
                }
            }
        };

        xhttp.open("DELETE", path + "/api/v1/project/delete/" + id, true);
        xhttp.setRequestHeader('Accept', 'application/json')
        xhttp.send();
    }
}

var skip = 0;
var count = 10;
lastHeight = 500;

function loadChangelog()
{
    xhttp.abort();
    showLoadingAnimation();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            var answer = JSON.parse(this.responseText);
            var logs = answer['changelog'];

            for (id in logs)
            {
                showChangeLog(logs[id]);
            }
            showPages(skip, count, answer['count']);
            hideLoadingAnimation();
        }
    };
    xhttp.open("GET", path + "/api/v1/project/changelog/" + projectId + "?skip=" + skip + "&count=" + count, true);
    xhttp.setRequestHeader('Accept', 'application/json')
    xhttp.send();
}

function showChangeLog(log)
{
    var logHtml = document.getElementById("templateEntry").cloneNode(true);
    logHtml.id = "";


    var date = dateToString(log.authorDateTimestamp * 1000);

    logHtml.innerHTML = logHtml.innerHTML.
    replace(/__MSG_AuthorName__/g, escapeHtml(log.authorName)).
    replace(/__MSG_AuthorAvatar__/g, escapeHtml(log.authorAvatar)).
    replace(/__MSG_AuthorDate__/g, date).
    replace(/__MSG_Subject__/g, escapeHtml(log.subject));

    document.getElementById('changelogList').appendChild(logHtml);
}

function dateToString(date)
{
    var d = new Date(date);

    var year = d.getFullYear();
    var month = "0" + d.getMonth();
    var date = "0" + d.getDate();
    var hour = "0" + d.getHours();
    var min = "0" + d.getMinutes();

    var time = date.substr(-2) + '.' + month.substr(-2) + '.' + year + ' ' + hour.substr(-2) + ':' + min.substr(-2);
    return time;
}
