// #region Dynamic CSS variables
{
const styleSheetId = 'edg-bricks-child-main'
const styleSheet = [...document.styleSheets].find(styleSheet => styleSheet.ownerNode.id === styleSheetId) ?? ''
const rule = styleSheet ? [...styleSheet.cssRules].find(rule => rule.selectorText === ':root') : ''
const props = {
	adminBar: '--edg-height-admin-bar',
	adminBarFixedOnly: '--edg-height-admin-bar-fixed-only',
	header: '--edg-height-header',
}
const fallback = '0px'

Object.keys(props).forEach(prop => {
	rule?.style?.setProperty(props[prop], fallback)
})

const setProp = (prop, value) => rule?.style?.setProperty(prop, value)

const getElementHeight = selector => {
	const element = document.querySelector(selector)

	return element ? `${element.getBoundingClientRect().height}px` : null
}

const updateProps = () => {
	const adminBar = document.querySelector('#wpadminbar')
	const adminBarHeight = getElementHeight('#wpadminbar')
	const headerHeight = getElementHeight('#brx-header')
	const { 
		adminBar: adminBarProp,
		adminBarFixedOnly: adminBarFixedOnlyProp,
		header: headerProp,
	 } = props
	const adminBarFixed = getComputedStyle(adminBar).position === 'fixed'
	setProp(adminBarProp, adminBarHeight)
	setProp(adminBarFixedOnlyProp, adminBarFixed ? adminBarHeight : fallback)
	setProp(headerProp, headerHeight)
}

updateProps()
window.addEventListener('resize', updateProps)
}
// #endregion Dynamic CSS variables


// #region Discard temporary URL params
{
const queryString = new URLSearchParams(location.search)
const params = [
	'utm_id',
	'utm_source',
	'utm_medium',
	'utm_campaign',
	'utm_term',
	'utm_content',
	'cn-reloaded',
]
params.forEach(param => {
	if (!queryString.has(param)) return

	queryString.delete(param)
})
const url = location.origin + location.pathname
const newUrl = queryString.toString() === '' ? url : `${url}?${queryString}`
history.replaceState({}, '', newUrl + location.hash)
}
// #endregion Discard temporary URL params


// #region Disable image dragging
document.addEventListener('dragstart', e => {
	if (!e.target.closest('img') || e.target.closest('[draggable="true"] > img, img[draggable="true"]')) return

	e.preventDefault()
})
// #endregion Disable image dragging


// #region Manage cookies toggle
document.addEventListener('click', e => {
	const selector = '.edg-manage-cookies'

	if (!e.target.closest(selector) || bricksbuilder?.isActivePanel()) return

	const element = e.target.closest(selector)
	element.innerText = edgBricksChild.translations['1']
	element.addEventListener('click', () => {
		location.reload()
	})
})
// #endregion Manage cookies toggle