import {useLocation} from "preact-iso";
import logo from "../assets/logo.svg";

export function Footer() {
    const { url } = useLocation();
    return (
        <div>
            <footer>
                <b><a href="https://www.vtdt.lv/" className="titlep" >Vidzemes Tehnoloģiju un dizaina tehnikums</a></b>
                <img src={logo} className="image" alt="VTDT Logo"></img>
            </footer>
        </div>
    );
}

export default Footer;