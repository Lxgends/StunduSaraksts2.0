import "../css/laiki.css"

function Laiki() {
    return (
        <div className="laikimain">
            <div className="pirmdiena">
                <strong className="titleb">Pirmdiena - Ceturtdiena</strong>
                <div className="classTime">
                    <strong className="classTitle"><p>1. Pārstunda</p></strong>
                    <strong><p>8:30 - 9:50</p></strong>
                </div>
                <div className="classTime">
                    <strong className="classTitle"><p>2. Pārstunda</p></strong>
                    <strong><p>10:10 - 11:30</p></strong>
                </div>
                <div className="pusdTime">
                    <strong className="breakTitle">Pusdienu partraukums</strong>
                    <strong><p>11:30 - 12:30</p></strong>
                </div>
                <div className="classTime">
                    <strong className="classTitle"><p>3. Pārstunda</p></strong>
                    <strong><p>12:30 - 13:50</p></strong>
                </div>
                <div className="classTime">
                    <strong className="classTitle"><p>4. Pārstunda</p></strong>
                    <strong><p>14:00 - 15:20</p></strong>
                </div>
                <div className="classTime">
                    <strong className="classTitle"><p>5. Pārstunda</p></strong>
                    <strong><p>15:30 - 16:50</p></strong>
                </div>
            </div>


            <div className="piektdiena">
                <strong className="titleb">Piektdiena</strong>
                <div className="classTime">
                    <strong className="classTitle"><p>1. Pārstunda</p></strong>
                    <strong><p>8:10 - 9:30</p></strong>
                </div>
                <div className="classTime">
                    <strong className="classTitle"><p>2. Pārstunda</p></strong>
                    <strong><p>9:40 - 11:00</p></strong>
                </div>
                <div className="classTime">
                    <strong className="classTitle"><p>3. Pārstunda</p></strong>
                    <strong><p>11:10 - 12:30</p></strong>
                </div>
                <div className="pusdTime">
                    <strong className="breakTitle">Pusdienu partraukums</strong>
                    <strong><p>12:30 - 13:00</p></strong>
                </div>
                <div className="classTime">
                    <strong className="classTitle"><p>4. Pārstunda</p></strong>
                    <strong><p>13:00 - 14:20</p></strong>
                </div>
                <div className="classTime">
                    <strong className="classTitle"><p>5. Pārstunda</p></strong>
                    <strong><p>14:30 - 15:50</p></strong>
                </div>
            </div>
        </div>
    );
}

export default Laiki;