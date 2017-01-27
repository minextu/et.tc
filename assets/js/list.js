var loading;
var list;
var lastHeight = 300;
var loadingStart;

function showLoadingAnimation()
{
    list = document.getElementsByClassName('list')[0];
    try
    {
        loading.parentNode.removeChild(loading);
    }
    catch (e)
    {
        if (list.children.length > 0)
            lastHeight = list.clientHeight;
    }

    loadingStart = new Date();

    list.innerHTML = "";
    list.setAttribute("transition", "off");
    list.style.opacity = 0;
    list.style.height = "0px";

    loading = document.createElement("div");
    loading.className = "loadingContainer";
    loadingDiv = document.createElement("div");
    loadingDiv.className = "loading";
    loading.appendChild(loadingDiv);
    loading.style.height = lastHeight + "px";
    list.parentNode.insertBefore(loading, list);
}

function hideLoadingAnimation()
{
    // try to show the loading animation atleast 1s
    var now = new Date();
    var timeout = 1000 - (now - loadingStart);
    if (timeout < 0)
        timeout = 0;

    window.setTimeout(function()
    {
        list.setAttribute("transition", "on");
        list.style.opacity = 1;
        list.style.height = "auto";
        loading.parentNode.removeChild(loading);
    }, timeout);
}

function updateSort(e)
{
    if (e.className == "active")
        return;

    updateActive(e);
    e.className = "active";
    sortBy = e.getAttribute("sortId");
    loadProjects();
}

function updateOrder(e)
{
    if (e.className == "active")
        return;

    updateActive(e);
    e.className = "active";
    order = e.getAttribute("sortId");
    loadProjects();
}

function updateActive(e)
{
    var nodes = e.parentNode.parentNode.children;
    for (i in nodes)
    {
        try
        {
            nodes[i].firstChild.className = "";
        }
        catch (e)
        {}
    }
}

function showPages(skip, currentCount, countAll)
{
    var pageContainer = document.getElementsByClassName("pageContainer")[0];
    pageContainer.innerHTML = "";

    var pages = Math.ceil(countAll / currentCount);

    for (var i = 1; i <= pages; i++)
    {
        var page = document.createElement("a");
        page.innerHTML = i + " ";
        page.setAttribute("pageNum", i);

        if (currentCount * (i - 1) == skip)
            page.className = "active";
        else
        {
            page.addEventListener("click", function(e)
            {
                goToPage(e.srcElement.getAttribute("pageNum"));
            });
        }

        pageContainer.appendChild(page);
    }
}

function goToPage(pageNum)
{
    skip = count * (pageNum - 1);
    loadChangelog();
}
