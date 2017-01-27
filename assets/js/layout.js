var menu_open = false;

function menu()
{
    if (menu_open)
    {
        document.getElementById("min_menu_entries").style.display = "none";
    }
    else
    {
        document.getElementById("min_menu_entries").style.display = "block";
    }

    menu_open = !menu_open;
}

function escapeHtml(html)
{
    var text = document.createTextNode(html);
    var div = document.createElement('div');
    div.appendChild(text);
    return div.innerHTML;
}
