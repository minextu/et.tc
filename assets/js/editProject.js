function initEditProject()
{
    if (!createDate)
        document.getElementById('gitCreateDateLink').style.display = "none";
    if (!updateDate)
        document.getElementById('gitUpdateDateLink').style.display = "none";
}

var xhttp = new XMLHttpRequest();

function deleteProjectImage(id)
{
    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4)
        {
            var answer = JSON.parse(this.responseText);
            if (answer['error'] !== undefined) alert("Error: " + answer['error']);
            else
            {
                document.getElementById('projectImage').src = answer['project']['image'];
            }
        }
    };
    xhttp.open("POST", path + "/api/v1/project/update/" + id, true);
    xhttp.setRequestHeader('Accept', 'application/json');
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("deleteImage=true");
}

function setCreateDateGit()
{
    document.getElementsByName("createDate")[0].value = createDate;
}

function setUpdateDateGit()
{
    document.getElementsByName("updateDate")[0].value = updateDate;
}
