function showHideApiKey(button)
{
    var key = button.parentNode.getElementsByTagName("input")[0];

    if (key.type == "text")
        key.type = "password";
    else
        key.type = "text";
}

var xhttp = new XMLHttpRequest();

function deleteApiKey(id)
{
    var status = confirm("Do you really want to delete this api Key?");
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
                    window.location.reload();
                }
            }
        };

        xhttp.open("POST", path + "/api/v1/user/deleteApiKey/" + id, true);
        xhttp.setRequestHeader('Accept', 'application/json')
        xhttp.send();
    }
}

function generateApiKey()
{
    var title = prompt("Title for the new api key");
    if (title)
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
                    window.location.reload();
                }
            }
        };

        xhttp.open("POST", path + "/api/v1/user/addApiKey/", true);
        xhttp.setRequestHeader('Accept', 'application/json');
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("title=" + encodeURIComponent(title));
    }
}
