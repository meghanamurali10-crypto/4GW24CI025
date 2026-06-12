const fs = require('fs');
const rs = require('readline-sync');

const file = 'students.json';

if (!fs.existsSync(file))
    fs.writeFileSync(file, '[]');

const read = () => JSON.parse(fs.readFileSync(file));
const write = data => fs.writeFileSync(file, JSON.stringify(data, null, 2));

while (true) {
    console.log("\n1.Create  2.Read  3.Update  4.Delete  5.Exit");
    let ch = rs.questionInt("Enter choice: ");
    let s = read();

    switch (ch) {

        case 1:
            s.push({
                usn: rs.question("USN: "),
                name: rs.question("Name: "),
                sem: rs.questionInt("Sem: "),
                year: rs.questionInt("Year: ")
            });
            write(s);
            console.log("Student Added");
            break;

        case 2:
            console.table(s);
            break;

        case 3:
            let u = rs.question("USN to update: ");
            let st = s.find(x => x.usn === u);

            if (st) {
                st.name = rs.question("New Name: ");
                st.sem = rs.questionInt("New Sem: ");
                write(s);
                console.log("Updated");
            } else
                console.log("Not Found");
            break;

        case 4:
            let d = rs.question("USN to delete: ");
            write(s.filter(x => x.usn !== d));
            console.log("Deleted");
            break;

        case 5:
            process.exit();

        default:
            console.log("Invalid Choice");
    }
}