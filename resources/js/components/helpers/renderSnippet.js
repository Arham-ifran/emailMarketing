const renderSnippet = (htmlSnippet, container_id) => {
    var checkoutContainer = document.getElementById(container_id)
    checkoutContainer.innerHTML = htmlSnippet
    var scriptsTags = checkoutContainer.getElementsByTagName('script')

    for (var i = 0; i < scriptsTags.length; i++) {
        var parentNode = scriptsTags[i].parentNode
        var newScriptTag = document.createElement('script')
        newScriptTag.type = 'text/javascript'
        newScriptTag.text = scriptsTags[i].text
        parentNode.removeChild(scriptsTags[i])
        parentNode.appendChild(newScriptTag)
    }
}

export default renderSnippet;