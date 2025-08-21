export default class TinyParallax {

	selector:string;
	offset:number;
	processId:number;

	constructor(selector: string, offset:number = 0) {
		this.selector = selector;
		this.offset = offset;
		// window.addEventListener("scroll", this.setBackgroundPosition.bind(this));
		this.setBackgroundPosition();
	}

	getElemTop(el:HTMLElement){
		const rect = el.getBoundingClientRect();
		const doc = el.ownerDocument;
		const win = doc.defaultView;
		const docElem = doc.documentElement;
		
		return rect.top + win.pageYOffset - docElem.clientTop
	}

	setBackgroundPosition(){
		document.querySelectorAll(this.selector).forEach((el: HTMLElement) => {
			const elTop = this.getElemTop(el);
			const pos = ((elTop - this.offset + el.clientHeight / 2 - window.scrollY) / window.innerHeight) * 100 + "%";
			el.style.backgroundPosition = "center " + pos
		});

		this.processId = requestAnimationFrame(this.setBackgroundPosition.bind(this));
	}
}
