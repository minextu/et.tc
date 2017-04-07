var sortBy = "updated";
var order = "desc";
var xhttp = new XMLHttpRequest();

function loadProjects()
{
    xhttp.abort();
    showLoadingAnimation();

    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            var projects = JSON.parse(this.responseText)['items'];
            for (id in projects)
            {
                showProject(projects[id]);
            }
            hideLoadingAnimation();
        }
    };
    xhttp.open("GET", path + "/api/v1/projects?sortBy=" + sortBy + "&order=" + order, true);
    xhttp.setRequestHeader('Accept', 'application/json')
    xhttp.send();
}

function showProject(project)
{
    var projectHtml = document.getElementById("templateEntry").cloneNode(true);
    projectHtml.id = project.id;

    // hide image, if it's just a placeholder
    //if (project.imageType == "Placeholder")
    //    projectHtml.getElementsByClassName('image')[0].style.display = "none";

    var createDate = dateToString(project.createDate);
    var updateDate = dateToString(project.updateDate);

    projectHtml.innerHTML = projectHtml.innerHTML.
    replace(/__MSG_ProjectId__/g, project.id).
    replace(/__MSG_ProjectTitle__/g, escapeHtml(project.title)).
    replace(/__MSG_ProjectDescription__/g, escapeHtml(project.description)).
    replace(/__MSG_ProjectImage__/g, project.image).
    replace(/__MSG_ProjectCreateDate__/g, createDate).
    replace(/__MSG_ProjectUpdateDate__/g, updateDate);



    document.getElementById('projectList').appendChild(projectHtml);
}

function dateToString(date)
{
    var date = new Date(date);
    var now = new Date();

    // less than 1 minute
    if (now - date < 1000 * 60 * 1)
        return "now";
    // less than 1 hour
    else if (now - date < 1000 * 60 * 60 * 1)
        return Math.round((now - date) / (1000 * 60)) + " minute(s)";
    // less than a day
    else if (now - date < 1000 * 60 * 60 * 24)
        return Math.round((now - date) / (1000 * 60 * 60)) + " hour(s)";
    // less than a month
    else if (now - date < 1000 * 60 * 60 * 24 * 30)
        return Math.round((now - date) / (1000 * 60 * 60 * 24)) + " day(s)";
    // less than a year
    else if (now - date < 1000 * 60 * 60 * 24 * 365)
        return Math.round((now - date) / (1000 * 60 * 60 * 24 * 30)) + " month(s)";
    else
        return Math.round((now - date) / (1000 * 60 * 60 * 24 * 365)) + " year(s)";
}

loadProjects();
