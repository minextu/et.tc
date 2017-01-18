function loadProjects()
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            var projects = JSON.parse(this.responseText)['items'];
            for (id in projects)
            {
                showProject(projects[id]);
            }
        }
    };
    xhttp.open("GET", path + "/api/v1/projects", true);
    xhttp.send();
}

function showProject(project)
{
    var projectHtml = document.getElementById("templateEntry").cloneNode(true);
    projectHtml.id = project.id;

    // hide image, if it's just a placeholder
    if (project.imageType == "Placeholder")
        projectHtml.getElementsByClassName('image')[0].style.display = "none";

    projectHtml.innerHTML = projectHtml.innerHTML.
    replace(/__MSG_ProjectId__/g, project.id).
    replace(/__MSG_ProjectTitle__/g, project.title).
    replace(/__MSG_ProjectDescription__/g, project.description).
    replace(/__MSG_ProjectImage__/g, project.image).
    replace(/__MSG_ProjectCreateDate__/g, project.createDate).
    replace(/__MSG_ProjectUpdateDate__/g, project.updateDate);



    document.getElementById('projectList').appendChild(projectHtml);
}

loadProjects();
