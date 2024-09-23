import { useLocation } from 'preact-iso';
import { useEffect } from "react";
import { myDropdown, dropdownFunction, setupDropdownCloseListener } from "./DropdownComponent.jsx"
import logo from "../assets/logo.svg"
import "../css/dropdown.css"


export function Header() {
	const { url } = useLocation();
	useEffect(() => {
		setupDropdownCloseListener();
	}, []);

	return (
		<header>
			<img src={ logo } className="image" placeholder="logo"></img>
			<nav>
				<div className="dropdown">
					<button onClick={() => myDropdown('dropdown1')} className="dropbtn">Kurss</button>
					<div id="dropdown1" className="dropdown-content">
						<input type="text" placeholder="Search.." id="myInput1" className="myInput"
							   onKeyUp={() => dropdownFunction('myInput1', 'dropdown1')} />
						<a href="/#ipb21">IPb21</a>
						<a href="/#ipa21">IPa21</a>
						<a href="/#km21">KM21</a>
					</div>
				</div>

				<div className="dropdown">
					<button onClick={() => myDropdown('dropdown2')} className="dropbtn">Pasniedzejs</button>
					<div id="dropdown2" className="dropdown-content">
						<input type="text" placeholder="Search.." id="myInput2" className="myInput"
							   onKeyUp={() => dropdownFunction('myInput2', 'dropdown2')} />
						<a href="/pasniedzejs#jkrigerts">J. Krigerts</a>
						<a href="/pasniedzejs#aievins">A. Ievins</a>
					</div>
				</div>

				<div className="dropdown">
					<button onClick={() => myDropdown('dropdown3')} className="dropbtn">Kabinets</button>
					<div id="dropdown3" className="dropdown-content">
						<input type="text" placeholder="Search.." id="myInput3" className="myInput"
							   onKeyUp={() => dropdownFunction('myInput3', 'dropdown3')} />
						<a href="/kabinets#301">301</a>
						<a href="/kabinets#201">201</a>
						<a href="/kabinets#199">199</a>
					</div>
				</div>
				<a href="/laiki" className={url == '/laiki' && 'active'} style="font-weight: bold;">
					Stundu Laiki
				</a>
			</nav>
		</header>
	);
}
